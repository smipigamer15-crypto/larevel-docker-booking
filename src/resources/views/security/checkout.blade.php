@extends('layouts.app')
@section('style-page', 'security/checkout')

@section('title', __('Checkout'))

@section('content')
    <div class="checkout-page">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-xl-6">

                    <div class="text-center mb-4" data-aos="fade-down">
                        <h1 class="fw-bold checkout-title">
                            {{ __('Complete Your') }} <span class="gradient-text">{{ __('Subscription') }}</span>
                        </h1>
                        <p class="text-secondary">{{ __('You\'re one step away from securing your business') }}</p>
                    </div>

                    <div class="checkout-card card border-0 shadow-lg" data-aos="fade-up" data-aos-delay="100">

                        <div class="plan-header">
                            <div class="d-flex align-items-center gap-3">
                                <div class="plan-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0">
                                        {{ $package === 'pro' ? __('Pro Security') : __('Enterprise Security') }}
                                    </h4>
                                    <p class="text-secondary small mb-0">
                                        {{ $package === 'pro' ? __('Advanced security for growing businesses') : __('Complete security for large enterprises') }}
                                    </p>
                                </div>
                            </div>
                            <div class="plan-price">
                                <span class="price-amount">${{ $package === 'pro' ? '49' : '199' }}</span>
                                <span class="price-period">{{ __('/month') }}</span>
                            </div>
                        </div>

                        <div class="features-preview">
                            <div class="row g-2">
                                @if($package === 'pro')
                                    @foreach([__('SSL Encryption'), '2FA Authentication', '24/7 Monitoring', __('Daily Backups')] as $feature)
                                        <div class="col-6">
                                            <div class="feature-mini">
                                                <i class="fas fa-check-circle text-success"></i>
                                                <span>{{ $feature }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    @foreach([__('All Pro features'), __('Advanced Firewall'), __('Priority Support'), __('API Access')] as $feature)
                                        <div class="col-6">
                                            <div class="feature-mini">
                                                <i class="fas fa-check-circle text-success"></i>
                                                <span>{{ $feature }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="divider">
                            <span>{{ __('Billing Information') }}</span>
                        </div>

                        @if(session('error'))
                            <div class="alert alert-danger rounded-3 mb-4">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ __(session('error')) }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('security.checkout.post', ['package' => $package]) }}" class="checkout-form">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-user me-2"></i>{{ __('Full Name') }}
                                        </label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ auth()->user()->name ?? '' }}"
                                            placeholder="{{ __('John Doe') }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-envelope me-2"></i>{{ __('Email') }}
                                        </label>
                                        <input type="email" class="form-control" name="email"
                                            value="{{ auth()->user()->email ?? '' }}"
                                            placeholder="john@example.com" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-phone me-2"></i>{{ __('Phone') }}
                                        </label>
                                        <input type="text" class="form-control" name="phone"
                                            value="{{ auth()->user()->phone ?? '' }}"
                                            placeholder="+1 (555) 123-4567">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">
                                            <i class="fas fa-building me-2"></i>{{ __('Company') }}
                                        </label>
                                        <input type="text" class="form-control" name="company"
                                            placeholder="{{ __('Your Company') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="secure-info">
                                <div class="secure-info-item">
                                    <i class="fas fa-lock"></i>
                                    <span>{{ __('256-bit SSL encryption') }}</span>
                                </div>
                                <div class="secure-info-item">
                                    <i class="fas fa-shield-alt"></i>
                                    <span>{{ __('Powered by Stripe') }}</span>
                                </div>
                                <div class="secure-info-item">
                                    <i class="fas fa-undo"></i>
                                    <span>{{ __('Cancel anytime') }}</span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary rounded-pill w-100 py-3 btn-checkout">
                                <i class="fas fa-credit-card me-2"></i>{{ __('Continue to Payment') }}
                            </button>

                            <div class="text-center mt-3">
                                <a href="{{ route('security') }}" class="cancel-link">
                                    <i class="fas fa-arrow-left me-2"></i>{{ __('Cancel and go back') }}
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="trust-badges text-center mt-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="d-flex justify-content-center align-items-center gap-4 flex-wrap">
                            <div class="trust-badge">
                                <i class="fas fa-shield-alt"></i>
                                <span>{{ __('Stripe Verified') }}</span>
                            </div>
                            <div class="trust-badge">
                                <i class="fas fa-lock"></i>
                                <span>{{ __('SSL Secured') }}</span>
                            </div>
                            <div class="trust-badge">
                                <i class="fas fa-headset"></i>
                                <span>{{ __('24/7 Support') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('styles')
    @vite('resources/css/security.css')
@endpush
