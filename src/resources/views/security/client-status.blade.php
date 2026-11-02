@extends('layouts.app')
@section('style-page', 'security/client-status')

@section('title', __('Security Status'))

@section('content')
    <div class="client-status-page">

        <div class="status-hero">
            <div class="container">
                <div class="text-center" data-aos="fade-down">
                    <h1 class="fw-bold hero-title">
                        {{ __('Security Subscription') }} <span class="gradient-text">{{ __('Status') }}</span>
                    </h1>
                    <p class="text-secondary fs-5">{{ __('Here is the current status of your security subscription') }}</p>
                </div>
            </div>
        </div>

        <div class="container pb-5">
            <div class="row justify-content-center">
                <div class="col-lg-9">

                    <div class="status-card" data-aos="fade-up">

                        <div class="status-header">
                            <div class="status-icon-wrapper">
                                @if($inquiry->is_active)
                                    <div class="status-icon-circle success">
                                        <i class="fas fa-shield-alt"></i>
                                        <div class="pulse-ring"></div>
                                    </div>
                                @elseif($inquiry->status == 'pending')
                                    <div class="status-icon-circle warning">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                @elseif($inquiry->status == 'deactivated')
                                    <div class="status-icon-circle danger">
                                        <i class="fas fa-times-circle"></i>
                                    </div>
                                @else
                                    <div class="status-icon-circle primary">
                                        <i class="fas fa-info-circle"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="status-badges">
                                <span class="badge-status status-{{ $inquiry->status ?? 'pending' }}">
                                    <i class="fas {{ $inquiry->is_active ? 'fa-check-circle' : 'fa-clock' }} me-2"></i>
                                    {{ __(ucfirst($inquiry->status ?? 'pending')) }}
                                </span>
                                @if($inquiry->is_active)
                                    <span class="badge-status status-active">
                                        <i class="fas fa-check-circle me-2"></i>{{ __('Active') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="plan-summary">
                            <div class="row g-0">
                                <div class="col-md-4 plan-summary-item">
                                    <div class="plan-summary-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div>
                                        <span class="summary-label">{{ __('Plan') }}</span>
                                        <h5 class="fw-bold mb-0">{{ $inquiry->package_label }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-4 plan-summary-item">
                                    <div class="plan-summary-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <span class="summary-label">{{ __('Client') }}</span>
                                        <h5 class="summary-value">{{ $inquiry->name }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-4 plan-summary-item">
                                    <div class="plan-summary-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div>
                                        <span class="summary-label">{{ __('Email') }}</span>
                                        <h5 class="summary-value">{{ Str::limit($inquiry->email, 20) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($inquiry->is_active && $inquiry->expires_at)
                            @php
                                $daysLeft = (int) ceil(now()->diffInDays($inquiry->expires_at, false));
                            @endphp
                            <div class="expiration-banner
                                    @if($daysLeft <= 3) expiring-danger
                                    @elseif($daysLeft <= 7) expiring-warning
                                    @else expiring-success
                                    @endif">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="expiration-icon">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div>
                                            <span class="expiration-label">{{ __('Renews in') }}</span>
                                            <h4 class="expiration-days">{{ $daysLeft }} {{ __('days') }}</h4>
                                        </div>
                                    </div>
                                    <div class="text-md-end">
                                        <span class="expiration-label">{{ __('Next billing') }}</span>
                                        <h6 class="mb-0 fw-bold">
                                            {{ $inquiry->expires_at->translatedFormat('M d, Y') }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($inquiry->is_active)
                            <div class="success-message">
                                <div class="success-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">{{ __('Your subscription is active!') }}</h5>
                                    <p class="text-secondary mb-0">{{ __('Your security package is fully activated and protecting your business.') }}</p>
                                </div>
                            </div>

                            <div class="actions-section mt-4">
                                <a href="{{ route('security.report.download', $inquiry->access_token) }}" class="btn btn-primary btn-action">
                                    <i class="fas fa-download me-2"></i>{{ __('Download Report') }}
                                </a>
                                <form method="POST" action="{{ route('security.subscription.cancel', $inquiry->access_token) }}" id="cancelForm" class="d-inline">
                                    @csrf
                                    <button type="button" class="btn btn-outline-primary btn-action" data-view-action="security-client-status-1" >
                                        <i class="fas fa-times-circle me-2"></i>{{ __('Cancel Subscription') }}
                                    </button>
                                </form>
                            </div>

                        @elseif($inquiry->status == 'pending')
                            <div class="pending-state">
                                <div class="pending-progress">
                                    <div class="progress-step active">
                                        <div class="step-icon"><i class="fas fa-check"></i></div>
                                        <span>{{ __('Created') }}</span>
                                    </div>
                                    <div class="progress-line active"></div>
                                    <div class="progress-step current">
                                        <div class="step-icon"><i class="fas fa-credit-card"></i></div>
                                        <span>{{ __('Payment') }}</span>
                                    </div>
                                    <div class="progress-line"></div>
                                    <div class="progress-step">
                                        <div class="step-icon"><i class="fas fa-rocket"></i></div>
                                        <span>{{ __('Activate') }}</span>
                                    </div>
                                </div>
                                <h5 class="fw-bold mb-2">{{ __('Your subscription is pending') }}</h5>
                                <p class="text-secondary">{{ __('Complete your payment to activate your plan.') }}</p>

                                <a href="{{ route('security.checkout', ['package' => $inquiry->package]) }}" class="btn btn-primary btn-lg-action">
                                    <i class="fas fa-credit-card me-2"></i>{{ __('Complete Payment') }}
                                </a>
                            </div>

                        @elseif($inquiry->status == 'deactivated')
                            <div class="danger-state">
                                <div class="danger-icon">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                                <h5 class="fw-bold mb-2">{{ __('Subscription deactivated') }}</h5>
                                <p class="text-secondary mb-4">{{ __('Please contact support or choose a new plan.') }}</p>
                                <a href="{{ route('security') }}" class="btn btn-primary btn-lg-action">
                                    <i class="fas fa-shield-alt me-2"></i>{{ __('Choose a Plan') }}
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="support-footer" data-aos="fade-up" data-aos-delay="200">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="support-item">
                                    <i class="fas fa-envelope"></i>
                                    <div>
                                        <span class="support-label">{{ __('Email') }}</span>
                                        <a href="mailto:support@booking.com">{{ __('support') }}@booking{{ __('.com') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="support-item">
                                    <i class="fas fa-phone"></i>
                                    <div>
                                        <span class="support-label">{{ __('Phone') }}</span>
                                        <a href="tel:+15551234567">+1 (555) 123-4567</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" data-page-script="security/client-status">

@endsection
@push('styles')
    @vite('resources/css/security.css')
@endpush
