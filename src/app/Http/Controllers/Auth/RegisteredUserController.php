<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\WelcomeEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $verificationSent = true;
        try {
            event(new Registered($user));
        } catch (\Throwable $exception) {
            $verificationSent = false;
            \Illuminate\Support\Facades\Log::warning('Registration verification email could not be delivered.', ['user_id' => $user->id]);
        }

        try {
            Mail::to($user->email)->send(new WelcomeEmail($user));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Welcome email failed: ' . $e->getMessage());
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($verificationSent) {
            $request->session()->put('verification_sent_at', now()->timestamp);

            return redirect()->route('verification.notice')->with('status', 'verification-link-sent');
        }

        return redirect()->route('verification.notice')->with('error', 'Your account is ready, but the verification email could not be delivered. Please try sending it again.');
    }
}
