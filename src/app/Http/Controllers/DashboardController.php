<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalBookings = Booking::where('user_id', $user->id)->count();

        $todayBookings = Booking::where('user_id', $user->id)
            ->where('booking_date', now()->toDateString())
            ->count();

        $upcomingBookings = Booking::where('user_id', $user->id)
            ->where('status', 'upcoming')
            ->where('booking_date', '>=', now()->toDateString())
            ->count();

        $completedBookings = Booking::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $recentBookings = Booking::where('user_id', $user->id)
            ->with('service')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $chartMonths = [];
        $chartBookings = [];
        $chartRevenue = [];
        $topServices = [];

        if ($user->hasActiveBookingSubscription()) {
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $chartMonths[] = $month->format('M');

                $count = Booking::where('user_id', $user->id)
                    ->whereMonth('bookings.created_at', $month->month)
                    ->whereYear('bookings.created_at', $month->year)
                    ->count();
                $chartBookings[] = $count;

                $rev = Booking::where('user_id', $user->id)
                    ->whereMonth('bookings.created_at', $month->month)
                    ->whereYear('bookings.created_at', $month->year)
                    ->join('services', 'bookings.service_id', '=', 'services.id')
                    ->sum('services.price');
                $chartRevenue[] = $rev;
            }

            $topServices = Booking::where('user_id', $user->id)
                ->select('service_id', DB::raw('count(*) as total'))
                ->groupBy('service_id')
                ->orderByDesc('total')
                ->limit(3)
                ->with('service')
                ->get();
        }

        return view('dashboard', compact('totalBookings', 'todayBookings', 'upcomingBookings', 'completedBookings', 'recentBookings', 'chartMonths', 'chartBookings', 'chartRevenue', 'topServices'));
    }
}
