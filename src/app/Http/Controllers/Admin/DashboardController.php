<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Service;
use App\Models\Category;
use App\Models\Review;
use App\Models\SecurityInquiry;
use App\Models\ActivityLog;
use App\Models\FirewallLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $lastBookings = session('last_viewed_bookings', now()->subYears(10));
        $lastServices = session('last_viewed_services', now()->subYears(10));
        $lastUsers = session('last_viewed_users', now()->subYears(10));
        $lastSecurity = session('last_viewed_security', now()->subYears(10));
        $lastMonitoring = session('last_viewed_monitoring', now()->subYears(10));
        $lastFirewall = session('last_viewed_firewall', now()->subYears(10));
        $lastCalendar = session('last_viewed_calendar', now()->subYears(10));
        $lastVulnerability = session('last_viewed_vulnerability', now()->subYears(10));
        $lastApi = session('last_viewed_api', now()->subYears(10));
        $lastCustomRules = session('last_viewed_custom_rules', now()->subYears(10));

        $validated = $request->validate([
            'days' => ['nullable', 'integer', 'in:7,30,90,365'],
            'start_date' => ['nullable', 'required_with:end_date', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'required_with:start_date', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);
        $days = (int) ($validated['days'] ?? 30);
        $end = Carbon::parse($validated['end_date'] ?? now()->toDateString())->endOfDay();
        $start = isset($validated['start_date']) ? Carbon::parse($validated['start_date'])->startOfDay() : $end->copy()->subDays($days - 1)->startOfDay();
        if ($start->diffInDays($end) > 366) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'start_date' => 'Choose a period of at most one year.',
            ]);
        }
        $startDate = $start->toDateString();
        $endDate = $end->toDateString();
        $selectedDays = !$request->filled('start_date') ? $days : null;

        $totalBookings = Booking::whereBetween('bookings.created_at', [$start, $end])->count();
        $totalUsers = User::whereBetween('users.created_at', [$start, $end])->count();
        $totalServices = Service::where('is_active', true)->count();
        $totalCategories = Category::count();

        $revenue = Booking::where('status', 'completed')
            ->whereBetween('bookings.created_at', [$start, $end])
            ->join('services', 'bookings.service_id', '=', 'services.id')
            ->sum('services.price');

        $todayBookings = Booking::whereDate('booking_date', Carbon::today())->count();
        $pendingBookings = Booking::where('status', 'upcoming')
            ->whereBetween('bookings.created_at', [$start, $end])
            ->count();
        $cancelledBookings = Booking::where('status', 'cancelled')
            ->whereBetween('bookings.created_at', [$start, $end])
            ->count();

        $recentBookings = Booking::with(['user', 'service'])
            ->whereBetween('bookings.created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $popularServices = Service::withCount(['bookings' => fn($query) => $query->whereBetween('created_at', [$start, $end])])
            ->orderBy('bookings_count', 'desc')
            ->limit(5)
            ->get();

        $recentUsers = User::whereBetween('users.created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentReviews = Review::with(['user', 'service'])
            ->whereBetween('reviews.created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $statusStats = [
            'upcoming' => Booking::where('status', 'upcoming')
                ->whereBetween('bookings.created_at', [$start, $end])
                ->count(),
            'completed' => Booking::where('status', 'completed')
                ->whereBetween('bookings.created_at', [$start, $end])
                ->count(),
            'cancelled' => Booking::where('status', 'cancelled')
                ->whereBetween('bookings.created_at', [$start, $end])
                ->count(),
        ];

        $totalSecurityInquiries = SecurityInquiry::whereBetween('created_at', [$start, $end])->count();
        $pendingSecurity = SecurityInquiry::where('status', 'pending')
            ->whereBetween('created_at', [$start, $end])
            ->count();
        $recentSecurityInquiries = SecurityInquiry::whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $bookingsByDay = Booking::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')->orderBy('date')->get()->keyBy('date');
        $usersByDay = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')->orderBy('date')->get()->keyBy('date');

        $chartData = ['labels' => [], 'values' => [], 'registrations' => []];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $key = $date->toDateString();
            $chartData['labels'][] = $key;
            $chartData['values'][] = (int) ($bookingsByDay->get($key)?->count ?? 0);
            $chartData['registrations'][] = (int) ($usersByDay->get($key)?->count ?? 0);
        }

        $badges = [
            'bookings' => Booking::where('created_at', '>', $lastBookings)
                ->where('status', 'upcoming')
                ->count(),
            'subscriptions' => \App\Models\BookingSubscription::where('status', 'pending')
                ->count(),
            'services' => Service::where('created_at', '>', $lastServices)
                ->where('is_active', false)
                ->count(),
            'users' => User::where('created_at', '>', $lastUsers)->count(),
            'security' => SecurityInquiry::where('created_at', '>', $lastSecurity)
                ->where('status', 'pending')
                ->count(),
            'monitoring' => ActivityLog::where('level', 'critical')
                ->where('created_at', '>', $lastMonitoring)
                ->count(),
            'firewall' => FirewallLog::where('created_at', '>', $lastFirewall)->count(),
            'calendar' => Booking::where('created_at', '>', $lastCalendar)
                ->whereDate('booking_date', today())
                ->count(),
            'vulnerability' => \App\Models\VulnerabilityScan::where('created_at', '>', $lastVulnerability)
                ->where('critical_checks', '>', 0)
                ->count(),
            'api' => \Laravel\Sanctum\PersonalAccessToken::where('created_at', '>', $lastApi)->count(),
            'custom_rules' => \App\Models\SecuritySetting::where('created_at', '>', $lastCustomRules)
                ->where('is_enabled', true)
                ->count(),
        ];

        $categoryStats = Category::withCount('services')
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'count' => $category->services_count,
                ];
            });

        $dailyCounts = collect($chartData['values'])->map(fn($count) => (object) ['count' => $count]);
        $bookingTrend = $this->getTrendPercentage($dailyCounts);

        return view('admin.dashboard', compact('selectedDays', 'totalBookings', 'totalUsers', 'totalServices', 'totalCategories', 'revenue', 'todayBookings', 'pendingBookings', 'cancelledBookings', 'recentBookings', 'popularServices', 'recentUsers', 'recentReviews', 'chartData', 'bookingTrend', 'startDate', 'endDate', 'statusStats', 'categoryStats', 'totalSecurityInquiries', 'pendingSecurity', 'recentSecurityInquiries', 'badges'));
    }

    private function getTrendPercentage($data)
    {
        if ($data->count() < 2) {
            return 0;
        }

        $current = $data->last()->count ?? 0;
        $previous = $data->slice(-2, 1)->first()->count ?? 0;

        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round(($current - $previous) / $previous * 100);
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'bookings');
        $format = $request->get('format', 'csv');

        if ($type == 'bookings') {
            $data = Booking::with(['user', 'service'])->get();
            $headers = ['ID', 'Service', 'User', 'Date', 'Time', 'Status', 'Created At'];
            $rows = $data->map(function ($booking) {
                return [
                    $booking->id,
                    $booking->service->title,
                    $booking->user->name,
                    $booking->booking_date,
                    $booking->start_time . ' - ' . $booking->end_time,
                    $booking->status,
                    $booking->created_at,
                ];
            });
        } elseif ($type == 'users') {
            $data = User::all();
            $headers = ['ID', 'Name', 'Email', 'Role', 'Created At'];
            $rows = $data->map(function ($user) {
                return [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->created_at,
                ];
            });
        } else {
            $data = Service::with('category')->get();
            $headers = ['ID', 'Title', 'Category', 'Price', 'Duration', 'Status'];
            $rows = $data->map(function ($service) {
                return [
                    $service->id,
                    $service->title,
                    $service->category->name ?? 'General',
                    $service->price,
                    $service->duration . ' min',
                    $service->is_active ? 'Active' : 'Inactive',
                ];
            });
        }

        $filename = $type . '_export_' . date('Y-m-d') . '.' . $format;

        if ($format == 'csv') {
            return $this->exportCsv($headers, $rows, $filename);
        } else {
            return $this->exportExcel($headers, $rows, $filename);
        }
    }

    private function exportCsv($headers, $rows, $filename)
    {
        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);

        foreach ($rows as $row) {
            fputcsv($output, $row);
        }

        fclose($output);

        return response()->stream(function () use ($headers, $rows) {
            $output = fopen('php://output', 'w');
            fputcsv($output, $headers);
            foreach ($rows as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function exportExcel($headers, $rows, $filename)
    {
        return $this->exportCsv($headers, $rows, $filename);
    }
}
