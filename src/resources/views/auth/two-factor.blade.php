@extends('layouts.app')
@section('style-page', 'auth/two-factor')

@section('title', __('Two-Factor Authentication'))

@section('content')
    <div class="two-factor-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <div class="two-factor-card">
                        <div class="text-center mb-4">
                            <div class="shield-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h2 class="fw-bold mt-3">{{ __('Two-Factor Verification') }}</h2>
                            <p class="text-secondary">
                                {{ __('Enter the 6-digit code sent to your email') }}
                            </p>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success rounded-pill">
                                <i class="fas fa-check-circle me-2"></i>{{ __(session('success')) }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger rounded-pill">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ __(session('error')) }}
                            </div>
                        @endif

                        <div class="timer-box" id="timerBox">
                            <div class="timer-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="timer-content">
                                <span class="timer-label">{{ __('Code expires in') }}</span>
                                <span class="timer-value" id="timerValue">60</span>
                                <span class="timer-unit">{{ __('seconds') }}</span>
                            </div>
                            <div class="timer-progress">
                                <div class="timer-progress-bar" id="timerProgress"></div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('2fa.verify') }}" id="verifyForm">
                            @csrf

                            <div class="mb-4">
                                <label for="code" class="form-label text-center d-block fw-semibold">
                                    {{ __('Verification Code') }}
                                </label>
                                <input type="text"
                                    class="form-control code-input @error('code') is-invalid @enderror"
                                    id="code"
                                    name="code"
                                    maxlength="6"
                                    pattern="[0-9]{6}"
                                    inputmode="numeric"
                                    autocomplete="one-time-code"
                                    autofocus
                                    required>
                                @error('code')
                                    <div class="invalid-feedback d-block text-center">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 mb-3" id="verifyBtn">
                                <i class="fas fa-check-circle me-2"></i>{{ __('Verify Code') }}
                            </button>
                        </form>

                        <div class="resend-section" id="resendSection">
                            <form method="POST" action="{{ route('2fa.resend') }}" class="d-inline" id="resendForm">
                                @csrf
                                <button type="submit" class="btn btn-link text-primary" id="resendBtn" disabled>
                                    <i class="fas fa-redo me-1"></i>
                                    <span id="resendText">{{ __('Resend available in 60s') }}</span>
                                </button>
                            </form>
                            <span class="text-secondary mx-2">·</span>
                            <a href="{{ route('login') }}" class="text-secondary text-decoration-none">
                                {{ __('Back to Login') }}
                            </a>
                        </div>

                        <div class="info-box mt-4">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>{{ __('Didn\'t receive the code?') }}</strong>
                                <p class="mb-0 small">{{ __('Check your spam folder or click "Resend" after the timer ends.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" data-page-script="auth/two-factor">
@endsection
@push('styles')
    @vite('resources/css/auth.css')
@endpush
