<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        \Illuminate\Auth\Notifications\VerifyEmail::toMailUsing(function (object $user, string $url) {
            return (new \Illuminate\Notifications\Messages\MailMessage())->subject('Confirm your email — Booking')
                ->view('emails.verify-email', [
                    'name' => $user->name,
                    'verificationUrl' => $url,
                    'expiresIn' => config('auth.verification.expire', 60),
                ]);
        });

        \App\Models\Booking::created(function ($booking) {
            \App\Jobs\SendBookingWebhook::dispatch($booking->id, 'booking.created')->afterResponse();
        });
        \App\Models\Booking::updated(function ($booking) {
            if ($booking->wasChanged('status')) {
                \App\Jobs\SendBookingWebhook::dispatch($booking->id, 'booking.updated')->afterResponse();
            }
        });

        RateLimiter::for('api-token', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many token requests. Please wait.',
                    ], 429);
                });
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'success' => false,
                        'message' => 'API rate limit exceeded. Max 60 requests per minute.',
                    ], 429);
                });
        });

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->view('errors.429', [], 429, $headers);
                });
        });

        RateLimiter::for('login', function (Request $request) {
            $key = $request->ip() . '|' . $request->input('email');

            return [
                Limit::perMinute(5)->by($key),
                Limit::perHour(30)->by($key),
            ];
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });

        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });

        RateLimiter::for('public', function (Request $request) {
            return Limit::perMinute(100)->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->view('errors.429', [], 429, $headers);
                });
        });

        RateLimiter::for('admin', function (Request $request) {
            return Limit::perMinute(300)->by($request->user()?->ip ?: $request->ip());
        });

        RateLimiter::for('booking-create', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->view('errors.429', [], 429, $headers);
                });
        });

        RateLimiter::for('contact', function (Request $request) {
            return Limit::perHour(5)->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->view('errors.429', [], 429, $headers);
                });
        });

        RateLimiter::for('strict', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->view('errors.429', [], 429, $headers);
                });
        });

        Paginator::defaultView('vendor.pagination.bootstrap-5');
    }
}
