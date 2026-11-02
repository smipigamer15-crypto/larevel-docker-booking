<?php

namespace App\Http\Middleware;

use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiLoggerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        $response = $next($request);

        $duration = (int) ((microtime(true) - $start) * 1000);

        try {
            ApiLog::create([
                'user_id' => $request->user()?->id,
                'token_id' => $request->user()?->currentAccessToken()?->id,
                'method' => $request->method(),
                'endpoint' => $request->path(),
                'status_code' => $response->getStatusCode(),
                'ip' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'response_time' => $duration,
            ]);
        } catch (\Throwable $e) {

        }

        $response->headers->set('X-Response-Time', $duration . 'ms');

        return $response;
    }
}
