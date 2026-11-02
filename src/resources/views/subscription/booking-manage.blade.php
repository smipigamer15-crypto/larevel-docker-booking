@extends('layouts.app')
@section('style-page', 'subscription/booking-manage')

@section('title', __('Booking Subscription Status'))

@section('content')
    <div class="client-status-page">

        <div class="status-hero">
            <div class="container">
                <div class="text-center" data-aos="fade-down">
                    <h1 class="fw-bold hero-title">
                        {{ __('Booking Subscription') }} <span class="gradient-text">{{ __('Status') }}</span>
                    </h1>
                    <p class="text-secondary fs-5">{{ __('Here is the current status of your booking subscription') }}</p>
                </div>
            </div>
        </div>

        <div class="container pb-5">
            <div class="row justify-content-center">
                <div class="col-lg-9">

                    <div class="status-card" data-aos="fade-up">

                        <div class="status-header">
                            <div class="status-icon-wrapper">
                                @if($subscription->is_active && !$subscription->cancel_at_period_end)
                                    <div class="status-icon-circle success">
                                        <i class="fas fa-crown"></i>
                                        <div class="pulse-ring"></div>
                                    </div>
                                @elseif($subscription->cancel_at_period_end)
                                    <div class="status-icon-circle warning">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                @elseif($subscription->status == 'pending')
                                    <div class="status-icon-circle warning">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                @elseif($subscription->status == 'deactivated')
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
                                <span class="badge-status status-{{ $subscription->status ?? 'pending' }}">
                                    <i class="fas {{ $subscription->is_active ? 'fa-check-circle' : 'fa-clock' }} me-2"></i>
                                    {{ __(ucfirst($subscription->status ?? 'pending')) }}
                                </span>
                                @if($subscription->is_active && !$subscription->cancel_at_period_end)
                                    <span class="badge-status status-active">
                                        <i class="fas fa-check-circle me-2"></i>{{ __('Active') }}
                                    </span>
                                @endif
                                @if($subscription->cancel_at_period_end)
                                    <span class="badge-status status-pending">
                                        <i class="fas fa-clock me-2"></i>{{ __('Cancelling') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="plan-summary">
                            <div class="row g-0">
                                <div class="col-md-4 plan-summary-item">
                                    <div class="plan-summary-icon">
                                        <i class="fas fa-crown"></i>
                                    </div>
                                    <div>
                                        <span class="summary-label">{{ __('Plan') }}</span>
                                        <h5 class="fw-bold mb-0">{{ $subscription->plan_label }} {{ __('Plan') }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-4 plan-summary-item">
                                    <div class="plan-summary-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <span class="summary-label">{{ __('Client') }}</span>
                                        <h5 class="summary-value">{{ $subscription->name }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-4 plan-summary-item">
                                    <div class="plan-summary-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div>
                                        <span class="summary-label">{{ __('Email') }}</span>
                                        <h5 class="summary-value">{{ Str::limit($subscription->email, 20) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($subscription->is_active && $subscription->subscription_ends_at)
                            @php
                                $daysLeft = (int) ceil(now()->diffInDays($subscription->subscription_ends_at, false));
                            @endphp
                            <div class="expiration-banner
                                    @if($subscription->cancel_at_period_end) expiring-warning
                                    @elseif($daysLeft <= 3) expiring-danger
                                    @elseif($daysLeft <= 7) expiring-warning
                                    @else expiring-success
                                    @endif">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="expiration-icon">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div>
                                            <span class="expiration-label">
                                                @if($subscription->cancel_at_period_end)
                                                    {{ __('Ends in') }}
                                                @else
                                                    {{ __('Renews in') }}
                                                @endif
                                            </span>
                                            <h4 class="expiration-days">{{ $daysLeft }} {{ __('days') }}</h4>
                                        </div>
                                    </div>
                                    <div class="text-md-end">
                                        <span class="expiration-label">
                                            @if($subscription->cancel_at_period_end)
                                                {{ __('Ends on') }}
                                            @else
                                                {{ __('Next billing') }}
                                            @endif
                                        </span>
                                        <h6 class="mb-0 fw-bold">
                                            {{ $subscription->subscription_ends_at->translatedFormat('M d, Y') }}
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($subscription->is_active && !$subscription->cancel_at_period_end)
                            <div class="success-message">
                                <div class="success-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">{{ __('Your subscription is active!') }}</h5>
                                    <p class="text-secondary mb-0">{{ __('You have full access to all features of your plan.') }}</p>
                                </div>
                            </div>

                            <div class="features-section">
                                <div class="features-header">
                                    <h5 class="fw-bold mb-0">
                                        <i class="fas fa-gift text-primary me-2"></i>{{ __('What\'s included') }}
                                    </h5>
                                    <span class="features-count">
                                        {{ $subscription->package === 'pro' ? '5' : '5' }} {{ __('features') }}
                                    </span>
                                </div>

                                <div class="row g-3">
                                    @php
                                        $planFeatures = $subscription->package === 'pro'
                                            ? [
                                                ['icon' => 'fa-infinity', 'label' => __('Unlimited bookings')],
                                                ['icon' => 'fa-sms', 'label' => __('SMS notifications')],
                                                ['icon' => 'fa-headset', 'label' => '24/7 support'],
                                                ['icon' => 'fa-plug', 'label' => __('Additional integrations')],
                                                ['icon' => 'fa-chart-line', 'label' => __('Analytics & reports')],
                                            ]
                                            : [
                                                ['icon' => 'fa-check-double', 'label' => __('All Pro features')],
                                                ['icon' => 'fa-code', 'label' => __('API access')],
                                                ['icon' => 'fa-users-cog', 'label' => __('Multiple administrators')],
                                                ['icon' => 'fa-bolt', 'label' => __('Priority support')],
                                                ['icon' => 'fa-paint-brush', 'label' => __('White-label branding')],
                                            ];
                                    @endphp
                                    @foreach($planFeatures as $feature)
                                        <div class="col-md-6">
                                            <div class="feature-card active">
                                                <div class="feature-icon">
                                                    <i class="fas {{ $feature['icon'] }}"></i>
                                                </div>
                                                <span class="feature-name">{{ $feature['label'] }}</span>
                                                <span class="feature-status active">{{ __('Active') }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="actions-section">
                                <a href="{{ route('booking.subscription.report', $subscription->access_token) }}" class="btn btn-primary btn-action">
                                    <i class="fas fa-download me-2"></i>{{ __('Download Subscription Report') }}
                                </a>
                                <form method="POST" action="{{ route('booking.subscription.cancel.subscription', $subscription->access_token) }}" id="cancelForm" class="d-inline">
                                    @csrf
                                    <button type="button" class="btn btn-outline-primary btn-action" data-view-action="subscription-booking-manage-1" >
                                        <i class="fas fa-times-circle me-2"></i>{{ __('Cancel Subscription') }}
                                    </button>
                                </form>
                            </div>

                        @elseif($subscription->cancel_at_period_end)
                            <div class="pending-state">
                                <div class="approved-icon" data-view-style="subscription-booking-manage-1">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h5 class="fw-bold mb-2">{{ __('Your subscription is cancelling') }}</h5>
                                <p class="text-secondary mb-4">
                                    {{ __('You\'ll keep access until') }} <strong>{{ $subscription->subscription_ends_at?->translatedFormat('M d, Y') }}</strong>.
                                </p>
                                <form method="POST" action="{{ route('booking.subscription.resume', $subscription->access_token) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-lg-action">
                                        <i class="fas fa-play-circle me-2"></i>{{ __('Resume Subscription') }}
                                    </button>
                                </form>
                            </div>

                        @elseif($subscription->status == 'pending')
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

                                <a href="{{ route('booking.subscription.checkout.page', ['package' => $subscription->package]) }}" class="btn btn-primary btn-lg-action">
                                    <i class="fas fa-credit-card me-2"></i>{{ __('Complete Payment') }}
                                </a>
                            </div>

                        @elseif($subscription->status == 'deactivated')
                            <div class="danger-state">
                                <div class="danger-icon">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                                <h5 class="fw-bold mb-2">{{ __('Subscription deactivated') }}</h5>
                                <p class="text-secondary mb-4">{{ __('Please contact support or choose a new plan.') }}</p>
                                <a href="{{ route('pricing') }}" class="btn btn-primary btn-lg-action">
                                    <i class="fas fa-crown me-2"></i>{{ __('Choose a Plan') }}
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

    <input type="hidden" data-page-script="subscription/booking-manage">

@endsection
@push('styles')
    @vite('resources/css/subscription.css')
@endpush
