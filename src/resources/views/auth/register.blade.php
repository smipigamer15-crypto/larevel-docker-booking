@extends('layouts.app')
@section('style-page', 'auth/register')

@section('title', __('Register'))

@section('content')
    <div class="auth-page">
        <div class="auth-container">
            <div class="auth-wrapper">

                <div class="auth-branding">
                    <div class="branding-content">
                        <div class="brand-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h1 class="brand-title">{{ __('Create Account') }}</h1>
                        <p class="brand-subtitle">
                            {{ __('Join thousands of satisfied customers and start booking your services today.') }}
                        </p>
                        <div class="brand-features">
                            <div class="feature-item">
                                <div class="feature-check">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>{{ __('Free registration') }}</span>
                            </div>
                            <div class="feature-item">
                                <div class="feature-check">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>{{ __('Book services anytime') }}</span>
                            </div>
                            <div class="feature-item">
                                <div class="feature-check">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>{{ __('Manage your bookings') }}</span>
                            </div>
                        </div>

                        <div class="brand-testimonial">
                            <div class="testimonial-quote">
                                <i class="fas fa-quote-left"></i>
                                <p>{{ __('"The best booking platform I\'ve ever used. Simple, fast, and reliable!"') }}</p>
                            </div>
                            <div class="testimonial-author">
                                <div class="author-avatar">
                                    <span>{{ __('JD') }}</span>
                                </div>
                                <div class="author-info">
                                    <div class="author-name">{{ __('John Doe') }}</div>
                                    <div class="author-role">{{ __('CEO, Tech Corp') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="branding-decoration">
                        <div class="deco-circle deco-1"></div>
                        <div class="deco-circle deco-2"></div>
                        <div class="deco-circle deco-3"></div>
                        <div class="deco-dots"></div>
                    </div>
                </div>

                <div class="auth-form">
                    <div class="form-content">
                        <div class="form-header">
                            <div class="form-icon">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <h2 class="form-title">{{ __('Create Account') }}</h2>
                            <p class="form-subtitle">{{ __('Join us and start booking') }}</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert-status" data-view-style="auth-register-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ __('Please fix the errors below.') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}" class="auth-form-fields">
                            @csrf

                            <div class="form-group">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user"></i>
                                    {{ __('Full Name') }}
                                </label>
                                <div class="input-wrapper">
                                    <input type="text"
                                        class="form-input @error('name') is-invalid @enderror"
                                        id="name"
                                        name="name"
                                        value="{{ old('name') }}"
                                        placeholder="{{ __('Enter your full name') }}"
                                        required
                                        autofocus>
                                    <span class="input-icon">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>
                                @error('name')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i>
                                    {{ __('Email Address') }}
                                </label>
                                <div class="input-wrapper">
                                    <input type="email"
                                        class="form-input @error('email') is-invalid @enderror"
                                        id="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        placeholder="{{ __('Enter your email') }}"
                                        required>
                                    <span class="input-icon">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                </div>
                                @error('email')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i>
                                    {{ __('Password') }}
                                </label>
                                <div class="input-wrapper">
                                    <input type="password"
                                        class="form-input @error('password') is-invalid @enderror"
                                        id="password"
                                        name="password"
                                        placeholder="{{ __('Create a password') }}"
                                        required>

                                    <button type="button" class="password-toggle" data-view-action="auth-register-1"  tabindex="-1">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">
                                    <i class="fas fa-check-circle"></i>
                                    {{ __('Confirm Password') }}
                                </label>
                                <div class="input-wrapper">
                                    <input type="password"
                                        class="form-input"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        placeholder="{{ __('Confirm your password') }}"
                                        required>
                                    <span class="input-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                </div>
                                @error('password_confirmation')
                                    <div class="form-error">
                                        <i class="fas fa-exclamation-circle"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <button type="submit" class="btn-submit">
                                <span>{{ __('Create Account') }}</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </form>

                        <div class="form-footer">
                            <p>
                                {{ __('Already have an account?') }}
                                <a href="{{ route('login') }}">{{ __('Sign In') }} <i class="fas fa-arrow-right"></i></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" data-page-script="auth/register">
@endsection
@push('styles')
    @vite('resources/css/auth.css')
@endpush
