<?php

namespace App\Http\Middleware;

use App\Services\MonitoringService;
use Closure;
use Illuminate\Http\Request;

class MonitoringMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($request->isMethod('POST') && $request->is('login')) {
            MonitoringService::checkSuspiciousActivity();
        }

        if ($response->getStatusCode() >= 500) {
            MonitoringService::error('server_error', 'Server error ' . $response->getStatusCode() . ' on ' . $request->fullUrl(), ['status' => $response->getStatusCode()]);
        }

        if ($response->getStatusCode() === 404) {
            $recent404 = \App\Models\ActivityLog::where('event', '404_error')
                ->where('created_at', '>=', now()->subMinute())
                ->where('ip_address', $request->ip())
                ->count();

            if ($recent404 >= 10) {
                MonitoringService::warning('excessive_404', 'Excessive 404 errors from IP ' . $request->ip(), ['count' => $recent404]);
            } else {
                MonitoringService::info('404_error', 'Page not found: ' . $request->fullUrl());
            }
        }

        return $response;
    }
}
