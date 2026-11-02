<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        if ((int) $request->session()->get('verification_sent_at', 0) + 60 > now()->timestamp) {
            return back()->with('info', 'A verification link was sent recently. Please check Mailhog.');
        }

        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (\Throwable $exception) {
            \Illuminate\Support\Facades\Log::warning('Verification email could not be delivered.', ['user_id' => $request->user()->id]);

            return back()->with('error', 'The verification email could not be sent. Check that Mailhog is running and try again.');
        }
        $request->session()->put('verification_sent_at', now()->timestamp);

        return back()->with('status', 'verification-link-sent');
    }
}
