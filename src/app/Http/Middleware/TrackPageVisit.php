<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageVisit
{
    protected array $trackedRoutes = [
        'admin.bookings.index' => 'last_viewed_bookings',
        'admin.bookings.show' => 'last_viewed_bookings',
        'admin.bookings.edit' => 'last_viewed_bookings',
        'admin.services.index' => 'last_viewed_services',
        'admin.services.show' => 'last_viewed_services',
        'admin.services.edit' => 'last_viewed_services',
        'admin.users.index' => 'last_viewed_users',
        'admin.users.show' => 'last_viewed_users',
        'admin.users.edit' => 'last_viewed_users',
        'admin.security.index' => 'last_viewed_security',
        'admin.security.show' => 'last_viewed_security',
        'admin.monitoring.index' => 'last_viewed_monitoring',
        'admin.monitoring.show' => 'last_viewed_monitoring',
        'admin.firewall.index' => 'last_viewed_firewall',
        'admin.firewall.logs' => 'last_viewed_firewall',
        'admin.calendar' => 'last_viewed_calendar',
        'admin.vulnerability.index' => 'last_viewed_vulnerability',
        'admin.vulnerability.show' => 'last_viewed_vulnerability',
        'admin.api-tokens.index' => 'last_viewed_api',
        'admin.custom-rules.index' => 'last_viewed_custom_rules',
        'admin.custom-rules.edit' => 'last_viewed_custom_rules',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $routeName = $request->route()?->getName();

        if ($routeName && isset($this->trackedRoutes[$routeName])) {
            session([
                $this->trackedRoutes[$routeName] => now()->toDateTimeString(),
            ]);
        }

        $response = $next($request);

        if ($request->is('admin/*') || $request->is('admin')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }

        return $response;
    }
}
