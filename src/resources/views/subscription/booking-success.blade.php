@extends('layouts.app')
@section('style-page', 'subscription/booking-success')

@section('title', __('Payment Successful'))

@section('content')
    <div class="success-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-xl-5">
                    <div class="success-card card border-0 shadow-lg text-center" data-aos="zoom-in">
                        <div class="success-icon-wrapper">
                            <div class="success-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="success-pulse"></div>
                            <div class="success-pulse pulse-2"></div>
                        </div>

                        <h1 class="fw-bold success-title">{{ __('Payment Successful!') }}</h1>
                        <p class="text-secondary mb-4">
                            {{ __('Welcome aboard! Your subscription is now active.') }}
                        </p>

                        <div class="plan-summary">
                            <div class="d-flex align-items-center gap-3">
                                <div class="plan-icon-success">
                                    <i class="fas fa-crown"></i>
                                </div>
                                <div class="text-start flex-grow-1">
                                    <h5 class="fw-bold mb-0">{{ $subscription->plan_label }} {{ __('Plan') }}</h5>
                                    <p class="text-secondary small mb-0">{{ __('Active subscription') }}</p>
                                </div>
                                <span class="badge-active">
                                    <i class="fas fa-check-circle me-1"></i>{{ __('Active') }}
                                </span>
                            </div>
                        </div>

                        <div class="details-grid">
                            <div class="detail-item">
                                <span class="detail-label">{{ __('Status') }}</span>
                                <span class="detail-value text-success">
                                    <i class="fas fa-circle" data-view-style="subscription-booking-success-1"></i>
                                    {{ __(ucfirst($subscription->subscription_status ?? __('Active'))) }}
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">{{ __('Next Billing') }}</span>
                                <span class="detail-value">
                                    {{ $subscription->subscription_ends_at?->translatedFormat('M d, Y') ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">{{ __('Amount') }}</span>
                                <span class="detail-value">
                                    ${{ $subscription->package === 'pro' ? '29' : '99' }}<span class="text-secondary">{{ __('/mo') }}</span>
                                </span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">{{ __('Client') }}</span>
                                <span class="detail-value">{{ $subscription->email }}</span>
                            </div>
                        </div>

                        <div class="actions">
                            @if($subscription->access_token)
                                <a href="{{ route('booking.subscription.manage', ['token' => $subscription->access_token]) }}"
                                        class="btn btn-primary rounded-pill w-100 py-3 btn-view-status">
                                    <i class="fas fa-sliders-h me-2"></i>{{ __('Manage Subscription') }}
                                </a>
                            @endif
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary rounded-pill w-100 py-3 mt-2">
                                <i class="fas fa-home me-2"></i>{{ __('Back to Dashboard') }}
                            </a>
                        </div>
                    </div>

                    <div class="info-message mt-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fas fa-info-circle text-primary"></i>
                            <div>
                                <strong>{{ __('A confirmation email has been sent') }}</strong>
                                <p class="text-secondary small mb-0">
                                    {{ __('Check your inbox for subscription details and next steps.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('styles')
    @vite('resources/css/subscription.css')
@endpush
