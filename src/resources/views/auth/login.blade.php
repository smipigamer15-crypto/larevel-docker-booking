@extends('layouts.app')
@section('style-page', 'auth/login')

@section('title', __('Login'))

@section('content')
    <div class="auth-page">
        <div class="auth-container">
            <div class="auth-wrapper">

                <div class="auth-branding">
                    <div class="branding-content">

                        <div class="brand-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>

                        <h1 class="brand-title">{{ __('Welcome Back!') }}</h1>
                        <p class="brand-subtitle">
                            {{ __('Sign in to continue your booking journey. Manage your appointments, track your history, and discover new services.') }}
                        </p>

                        <div class="brand-features">
                            <div class="feature-item">
                                <div class="feature-check">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>{{ __('Secure & encrypted login') }}</span>
                            </div>
                            <div class="feature-item">
                                <div class="feature-check">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>{{ __('Access your bookings anytime') }}</span>
                            </div>
                            <div class="feature-item">
                                <div class="feature-check">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>{{ __('24/7 customer support') }}</span>
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
                            <h2 class="form-title">{{ __('Welcome Back') }}</h2>
                            <p class="form-subtitle">{{ __('Sign in to your account') }}</p>
                        </div>

                        @if (session('status'))
                            <div class="alert-status">
                                <i class="fas fa-check-circle"></i>
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="auth-form-fields">
                            @csrf

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
                                        required
                                        autofocus>
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
                                        placeholder="{{ __('Enter your password') }}"
                                        required>
                                    <button type="button" class="password-toggle" data-view-action="auth-login-1"  tabindex="-1">
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

                            <div class="form-options">
                                <div class="form-check">
                                    <input type="checkbox" class="check-input" id="remember" name="remember">
                                    <label class="check-label" for="remember">{{ __('Remember me') }}</label>
                                </div>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="forgot-link">
                                        {{ __('Forgot password?') }}
                                    </a>
                                @endif
                            </div>

                            <button type="submit" class="btn-submit">
                                <span>{{ __('Sign In') }}</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </form>

                        <div class="form-footer">
                            <p>
                                {{ __('Don\'t have an account?') }}
                                <a href="{{ route('register') }}">{{ __('Sign Up') }} <i class="fas fa-arrow-right"></i></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" data-page-script="auth/login">
@endsection
@push('styles')
    @vite('resources/css/auth.css')
@endpush
