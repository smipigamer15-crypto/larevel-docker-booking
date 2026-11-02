<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Mail\AdminNewBooking;
use App\Mail\BookingCancelled;
use App\Mail\BookingConfirmation;
use App\Models\BlockedDate;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Services\BookingAvailability;
use App\Services\MonitoringService;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $bookings = Booking::where('user_id', $user->id)
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        $upcoming = $bookings->filter(function ($booking) {
            return $booking->status === 'upcoming' && $booking->booking_date->toDateString() >= now()->toDateString();
        })->values();

        $completed = $bookings->filter(function ($booking) {
            return $booking->status === 'completed' || $booking->status === 'upcoming' && $booking->booking_date->toDateString() < now()->toDateString();
        })->values();

        $cancelled = $bookings->where('status', 'cancelled')->values();

        return view('bookings.index', compact('upcoming', 'completed', 'cancelled'));
    }

    public function show(Booking $booking)
    {
        if ($booking->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('bookings.show', compact('booking'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'service_id' => [
                'nullable',
                'integer',
                Rule::exists('services', 'id')->where('is_active', true),
            ],
            'date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:today'],
            'time' => ['nullable', 'date_format:H:i'],
        ]);

        $services = Service::where('is_active', true)->get();
        $selectedService = null;

        $selectedServiceId = $request->old('service_id', $request->input('service_id'));
        if (is_scalar($selectedServiceId) && filter_var($selectedServiceId, FILTER_VALIDATE_INT)) {
            $selectedService = Service::where('is_active', true)->find($selectedServiceId);
        }

        return view('bookings.create', compact('services', 'selectedService'));
    }

    public function store(StoreBookingRequest $request, BookingAvailability $availability)
    {
        $user = $request->user();
        $booking = $availability->create($user, $request->validated());
        $service = $booking->service;

        MonitoringService::info('booking_created', 'New booking: '.$service->title.' on '.$request->booking_date, [
            'booking_id' => $booking->id,
            'service_id' => $service->id,
            'service_title' => $service->title,
            'booking_date' => $request->booking_date,
            'start_time' => $request->start_time,
            'amount' => $service->price,
        ]);

        try {
            Mail::to(Auth::user()->email)->send(new BookingConfirmation($booking));
        } catch (\Exception $e) {
            MonitoringService::error('email_failed', 'Booking confirmation email failed: '.$e->getMessage(), ['booking_id' => $booking->id, 'email' => Auth::user()->email]);
        }

        try {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new AdminNewBooking($booking));
            }
        } catch (\Exception $e) {
            MonitoringService::error('email_failed', 'Admin booking notification failed: '.$e->getMessage(), ['booking_id' => $booking->id]);
        }

        if ($user->hasActiveBookingSubscription() && $user->phone) {
            SmsService::send($user->phone, "Booking Confirmed: {$service->title} on {$request->booking_date} at {$request->start_time}.");
        }

        return redirect()->route('bookings.index')->with('success', 'Booking created successfully! Check your email for confirmation.');
    }

    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($booking->status === 'cancelled') {
            return back()->with('error', 'This booking is already cancelled.');
        }

        if ($booking->status === 'completed') {
            return back()->with('error', 'Cannot cancel a completed booking.');
        }

        $booking->update(['status' => 'cancelled']);

        MonitoringService::warning('booking_cancelled', 'Booking cancelled #'.$booking->id, [
            'booking_id' => $booking->id,
            'service_title' => $booking->service->title ?? null,
            'booking_date' => $booking->booking_date,
            'user_id' => $booking->user_id,
        ]);

        try {
            Mail::to($booking->user->email)->send(new BookingCancelled($booking));
        } catch (\Exception $e) {
            MonitoringService::error('email_failed', 'Booking cancellation email failed: '.$e->getMessage(), ['booking_id' => $booking->id]);
        }

        if ($booking->user->hasActiveBookingSubscription() && $booking->user->phone) {
            SmsService::send($booking->user->phone, "Booking Cancelled: {$booking->service->title} on {$booking->booking_date} at {$booking->start_time}.");
        }

        return back()->with('success', 'Booking cancelled successfully.');
    }

    public function getAvailableSlots(Request $request, BookingAvailability $availability)
    {
        $data = $request->validate([
            'service_id' => ['required', 'integer', Rule::exists('services', 'id')->where('is_active', true)],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
        ]);

        $service = Service::findOrFail($data['service_id']);
        if (BlockedDate::whereDate('date', $data['date'])->exists()) {
            return response()->json(['slots' => [], 'error' => __('This day is blocked')]);
        }

        return response()->json(['slots' => $availability->slots($service, $request->user(), $data['date'])]);
    }

    public function getCalendarData(Request $request)
    {
        $data = $request->validate([
            'year' => ['nullable', 'integer', 'between:1970,2100'],
            'month' => ['nullable', 'integer', 'between:1,12'],
        ]);
        $year = $data['year'] ?? now()->year;
        $month = $data['month'] ?? now()->month;

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $bookings = Booking::whereBetween('booking_date', [$startDate, $endDate])
            ->where('status', 'upcoming')
            ->get()
            ->groupBy(fn (Booking $booking) => $booking->booking_date->toDateString());

        $blockedDates = BlockedDate::whereBetween('date', [$startDate, $endDate])
            ->get()->map(fn (BlockedDate $blocked) => $blocked->date->toDateString())->all();

        $calendarData = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dateKey = $currentDate->format('Y-m-d');
            $calendarData[$dateKey] = [
                'bookings_count' => $bookings->get($dateKey, collect())->count(),
                'is_blocked' => in_array($dateKey, $blockedDates),
                'is_past' => $currentDate->lt(now()->startOfDay()),
            ];
            $currentDate->addDay();
        }

        return response()->json($calendarData);
    }
}
