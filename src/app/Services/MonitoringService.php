<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MonitoringService
{
    public static function log(string $event, string $description, string $level = 'info', array $metadata = [])
    {
        try {
            $log = ActivityLog::create([
                'user_id' => Auth::id(),
                'level' => $level,
                'event' => $event,
                'description' => $description,
                'ip_address' => request()->ip() ?? null,
                'user_agent' => request()->userAgent() ?? null,
                'url' => request()->fullUrl() ?? null,
                'method' => request()->method() ?? null,
                'metadata' => $metadata,
            ]);

            if ($level === 'critical') {
                self::notifyAdmin($log);
            }

            return $log;
        } catch (\Exception $e) {
            Log::error('Monitoring log failed: ' . $e->getMessage());
            return null;
        }
    }

    public static function info(string $event, string $description, array $metadata = [])
    {
        return self::log($event, $description, 'info', $metadata);
    }

    public static function warning(string $event, string $description, array $metadata = [])
    {
        return self::log($event, $description, 'warning', $metadata);
    }

    public static function error(string $event, string $description, array $metadata = [])
    {
        return self::log($event, $description, 'error', $metadata);
    }

    public static function critical(string $event, string $description, array $metadata = [])
    {
        return self::log($event, $description, 'critical', $metadata);
    }

    protected static function notifyAdmin(ActivityLog $log)
    {
        try {
            $adminEmail = config('mail.admin_email', 'admin@booking.com');

            Mail::raw("CRITICAL EVENT\n\n" . "Event: {$log->event}\n" . "Description: {$log->description}\n" . "IP: {$log->ip_address}\n" . "User: " . ($log->user->email ?? 'Guest') . "\n" . "URL: {$log->url}\n" . "Time: {$log->created_at}\n" . "\nMetadata: " . json_encode($log->metadata, JSON_PRETTY_PRINT), function ($message) use ($adminEmail) {
                $message->to($adminEmail)
                    ->subject('Critical Security Alert - ' . $log->event);
            });
        } catch (\Exception $e) {
            Log::error('Admin notification failed: ' . $e->getMessage());
        }
    }

    public static function checkSuspiciousActivity()
    {
        $failedLogins = ActivityLog::where('event', 'login_failed')
            ->where('created_at', '>=', now()->subMinutes(10))
            ->where('ip_address', request()->ip())
            ->count();

        if ($failedLogins >= 5) {
            self::critical('suspicious_login_attempts', "Multiple failed login attempts ({$failedLogins}) from IP " . request()->ip(), ['ip' => request()->ip(), 'count' => $failedLogins]);
        }

        if (Auth::check()) {
            $user = Auth::user();
            $currentIp = request()->ip();

            $knownIp = ActivityLog::where('user_id', $user->id)
                ->where('event', 'login')
                ->where('ip_address', $currentIp)
                ->exists();

            if (!$knownIp && ActivityLog::where('user_id', $user->id)->where('event', 'login')->exists()) {
                self::warning('login_new_ip', "Login from new IP: {$currentIp}", ['user_id' => $user->id, 'ip' => $currentIp]);
            }
        }
    }

    public static function getStats()
    {
        return [
            'total' => ActivityLog::count(),
            'last_24h' => ActivityLog::recent(24)->count(),
            'critical_24h' => ActivityLog::critical()->recent(24)->count(),
            'warnings_24h' => ActivityLog::where('level', 'warning')->recent(24)->count(),
            'errors_24h' => ActivityLog::where('level', 'error')->recent(24)->count(),
        ];
    }
}
