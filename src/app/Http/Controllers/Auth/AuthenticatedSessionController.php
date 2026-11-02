<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        \App\Services\MonitoringService::info('login', 'User logged in: ' . $user->email, ['user_id' => $user->id, 'ip' => $request->ip()]);

        if ($user->two_factor_enabled) {
            Auth::logout();
            session(['2fa:user:id' => $user->id]);
            $code = $user->generateTwoFactorCode();

            try {
                Mail::to($user->email)->send(new TwoFactorCodeMail($user, $code));
            } catch (\Exception $e) {
                Log::error('2FA email failed: ' . $e->getMessage());
            }

            return redirect()->route('2fa.show');
        }

        $redirect = $request->input('redirect');
        if (is_string($redirect) && !str_contains($redirect, '\\') && !preg_match('/[\x00-\x20]/', $redirect) && (str_starts_with($redirect, '/') && !str_starts_with($redirect, '//') || str_starts_with($redirect, url('/') . '/'))) {
            return redirect($redirect);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user) {
            \App\Services\MonitoringService::info('logout', 'User logged out: ' . $user->email, ['user_id' => $user->id]);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
