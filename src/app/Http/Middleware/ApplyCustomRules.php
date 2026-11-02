<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApplyCustomRules
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        $settings = $user->securitySetting;

        if (!$settings || !$settings->is_enabled) {
            return $next($request);
        }

        $ip = $request->ip();

        if ($settings->isIpBlacklisted($ip)) {
            return $this->deny($request, 'Your IP is blacklisted', 'custom_rule_ip_blacklist');
        }

        if (!$settings->isIpWhitelisted($ip)) {
            return $this->deny($request, 'Your IP is not in the whitelist', 'custom_rule_ip_whitelist');
        }

        if (!$settings->isHourAllowed()) {
            return $this->deny($request, 'Access is only allowed between ' . $settings->allowed_hours_label, 'custom_rule_hour_restriction');
        }

        if (!$settings->isDayAllowed()) {
            return $this->deny($request, 'Access is only allowed on: ' . $settings->allowed_days_label, 'custom_rule_day_restriction');
        }

        return $next($request);
    }

    protected function deny(Request $request, string $message, string $event): Response
    {
        try {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'level' => 'warning',
                'event' => $event,
                'description' => $message . ' — IP: ' . $request->ip(),
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ]);
        } catch (\Throwable $e) {

        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => 'custom_rule_violation',
            ], 403);
        }

        abort(403, $message);
    }
}
