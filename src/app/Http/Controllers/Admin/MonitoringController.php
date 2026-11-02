<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('event', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('subscription')) {
            $subFilter = $request->subscription;
            $query->whereHas('user', function ($q) use ($subFilter) {
                $q->whereHas('bookingSubscriptions', function ($subQ) use ($subFilter) {
                    $subQ->where('is_active', true);
                    if (in_array($subFilter, ['pro', 'enterprise'])) {
                        $subQ->where('package', $subFilter);
                    }
                });
            });
        }

        $logs = $query->latest()->paginate(20)->appends($request->query());
        $users = User::orderBy('name')->get();

        $stats = [
            'total' => ActivityLog::count(),
            'last_24h' => ActivityLog::where('created_at', '>=', now()->subDay())->count(),
            'warnings_24h' => ActivityLog::where('level', 'warning')->where('created_at', '>=', now()->subDay())->count(),
            'errors_24h' => ActivityLog::where('level', 'error')->where('created_at', '>=', now()->subDay())->count(),
            'critical_24h' => ActivityLog::where('level', 'critical')->where('created_at', '>=', now()->subDay())->count(),
        ];

        return view('admin.monitoring.index', compact('logs', 'stats', 'users'));
    }

    public function show($id)
    {
        $log = ActivityLog::with('user')->findOrFail($id);
        return view('admin.monitoring.show', compact('log'));
    }

    public function clear(Request $request)
    {
        $days = (int) $request->input('days', 30);

        if ($days === 0) {
            ActivityLog::query()->delete();
            return back()->with('success', 'All logs have been cleared successfully!');
        }

        ActivityLog::where('created_at', '<', now()->subDays($days))->delete();

        return back()->with('success', "Logs older than {$days} days have been cleared!");
    }

    public function clearAll()
    {
        ActivityLog::query()->delete();
        return back()->with('success', 'All activity logs have been completely deleted.');
    }
}
