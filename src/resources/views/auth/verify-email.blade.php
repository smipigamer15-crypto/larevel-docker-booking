@extends('layouts.app')
@section('style-page', 'auth/verify-email')
@section('title', __('Verify your email'))
@section('content')
    <section class="verification-page"><div class="container"><div class="verification-layout">
                <div class="verification-intro"><span class="feature-category-pill"><i class="far fa-envelope"></i> {{ __('ONE SMALL STEP') }}</span><h1>{{ __('You\'re almost') }}<br><span>{{ __('all set.') }}</span></h1><p>{{ __('Confirm your email to unlock your account\'s tools and keep your bookings connected.') }}</p><div class="verification-checks"><span><i class="fas fa-check-circle"></i> {{ __('A secure, personal account') }}</span><span><i class="fas fa-check-circle"></i> {{ __('Your plans and integrations, connected') }}</span><span><i class="fas fa-check-circle"></i> {{ __('Important updates in one place') }}</span></div></div>
                <div class="verification-card"><span class="verification-envelope"><i class="far fa-envelope-open"></i></span><h2>{{ __('Check your inbox') }}</h2><p>{{ session('verification_sent_at') ? 'A confirmation link was sent to:' : __('Send a confirmation link to:') }}</p><strong class="verification-address">{{ auth()->user()->email }}</strong><p class="verification-help">{{ __('Open the message and choose') }} <strong>{{ __('Confirm my email address') }}</strong>{{ __('. The link is valid for') }} {{ config('auth.verification.expire', 60) }} {{ __('minutes.') }}</p>
                    @if(app()->environment('local'))<a href="http://localhost:8025" target="_blank" rel="noopener noreferrer" class="btn btn-primary w-100 mb-3"><i class="far fa-envelope me-2"></i>{{ __('Open local Mailhog') }}</a><p class="verification-local-note">{{ __('Local development: messages arrive in Mailhog, not in an external inbox.') }}</p>@endif
                    <form method="POST" action="{{ route('verification.send') }}">@csrf<button type="submit" class="btn btn-outline-primary w-100" data-verification-resend data-cooldown="{{ max(0, (int) session('verification_sent_at', 0) + 60 - now()->timestamp) }}">{{ __('Send another verification email') }}</button><p class="small text-secondary mt-2" data-verification-countdown role="timer" aria-live="off"></p></form>
                    <div class="verification-card-footer"><a href="{{ route('profile.edit') }}#account">{{ __('Change email address') }}</a><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit">{{ __('Sign out') }}</button></form></div>
                </div>
    </div></div></section>
@endsection
@push('styles')
    @vite('resources/css/auth.css')
@endpush
