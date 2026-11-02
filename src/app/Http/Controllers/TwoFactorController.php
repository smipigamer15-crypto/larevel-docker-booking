<?php

namespace App\Http\Controllers;

use App\Mail\TwoFactorCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TwoFactorController extends Controller
{
    public function show()
    {
        if (!session('2fa:user:id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = session('2fa:user:id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->verifyTwoFactorCode($request->code)) {
            return back()->withErrors(['code' => 'Invalid or expired code.']);
        }

        $user->resetTwoFactorCode();
        $user->two_factor_verified_at = now();
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();

        session()->forget('2fa:user:id');

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Welcome back!');
    }

    public function resend()
    {
        $userId = session('2fa:user:id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = \App\Models\User::find($userId);
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->two_factor_code && $user->two_factor_expires_at && $user->two_factor_expires_at->isFuture()) {
            if (!$user->canResendTwoFactorCode()) {
                $seconds = $user->two_factor_seconds_remaining;
                return back()->with('error', "Please wait {$seconds} seconds before requesting a new code.");
            }
        }

        $code = $user->generateTwoFactorCode();

        try {
            Mail::to($user->email)->send(new TwoFactorCodeMail($user, $code));

            return back()->with('success', 'A new code has been sent to your email.');
        } catch (\Exception $e) {
            Log::error('2FA email failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to send code. Please try again.');
        }
    }

    public function enable(Request $request)
    {
        $user = Auth::user();

        if (!$user->canUseTwoFactor()) {
            return back()->with('error', 'Two-factor authentication is not available for your current plan.');
        }

        $user->two_factor_enabled = true;
        $user->save();

        return back()->with('success', 'Two-factor authentication enabled successfully!');
    }

    public function disable(Request $request)
    {
        $user = Auth::user();

        $user->two_factor_enabled = false;
        $user->resetTwoFactorCode();
        $user->save();

        return back()->with('success', 'Two-factor authentication disabled.');
    }
}
