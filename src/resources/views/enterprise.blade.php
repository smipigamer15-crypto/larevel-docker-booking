@extends('layouts.app')
@section('style-page', 'enterprise')

@section('title', __('Enterprise'))

@section('content')
    <div class="enterprise-page marketing-page">
        <x-marketing-hero eyebrow="A LITTLE MORE POSSIBILITY" title="{{ __('Room for your') }}" accent="next big move." description="Bring your team, services and scheduling together with booking tools that fit the way your business works." icon="fa-building" tone="purple">
            <x-slot:actions><a href="{{ route('resources.show', 'live-chat') }}" class="btn btn-primary">{{ __('Talk to our team') }} <i class="fas fa-arrow-right"></i></a><a href="{{ route('resources.show', 'admin-demo') }}" class="btn btn-outline-primary">{{ __('Explore the demo') }}</a></x-slot:actions>
        </x-marketing-hero>

        <section class="stats-section py-5 bg-light">
            <div class="container">
                <div class="row g-4 text-center">
                    <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="0">
                        <div class="stat-card">
                            <h2 class="display-4 fw-bold text-primary">99.9%</h2>
                            <h5 class="fw-bold">{{ __('Uptime Guarantee') }}</h5>
                            <p class="text-secondary small">{{ __('Reliable infrastructure with SLA-backed uptime') }}</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="stat-card">
                            <h2 class="display-4 fw-bold text-primary">24/7</h2>
                            <h5 class="fw-bold">{{ __('Support') }}</h5>
                            <p class="text-secondary small">{{ __('Round-the-clock dedicated support team') }}</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="stat-card">
                            <h2 class="display-4 fw-bold text-primary">500+</h2>
                            <h5 class="fw-bold">{{ __('Integrations') }}</h5>
                            <p class="text-secondary small">{{ __('Connect with your favorite business tools') }}</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="stat-card">
                            <h2 class="display-4 fw-bold text-primary">1M+</h2>
                            <h5 class="fw-bold">{{ __('Bookings') }}</h5>
                            <p class="text-secondary small">{{ __('Process millions of bookings with ease') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="features-section py-5">
            <div class="container">
                <div class="text-center mb-5" data-aos="fade-down">
                    <h2 class="display-5 fw-bold">{{ __('Everything You Need to Scale') }}</h2>
                    <p class="text-secondary fs-5">{{ __('Powerful tools designed for enterprise-level operations') }}</p>
                </div>

                <div class="row g-4">
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="0">
                        <div class="feature-card card border-0 shadow-sm h-100 p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-building text-primary"></i>
                            </div>
                            <h5 class="fw-bold">{{ __('Multi-Branch Management') }}</h5>
                            <p class="text-secondary">{{ __('Manage multiple locations, departments, and teams from one centralized dashboard. Perfect for franchises and chains.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="feature-card card border-0 shadow-sm h-100 p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-shield-alt text-success"></i>
                            </div>
                            <h5 class="fw-bold">{{ __('Advanced Security') }}</h5>
                            <p class="text-secondary">{{ __('Enterprise-grade security with SSO integration, role-based access control, and comprehensive audit logs.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="feature-card card border-0 shadow-sm h-100 p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-chart-bar text-info"></i>
                            </div>
                            <h5 class="fw-bold">{{ __('Advanced Analytics') }}</h5>
                            <p class="text-secondary">{{ __('Deep insights with custom reports, predictive analytics, and real-time business intelligence dashboards.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="feature-card card border-0 shadow-sm h-100 p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-headset text-warning"></i>
                            </div>
                            <h5 class="fw-bold">{{ __('Dedicated Support') }}</h5>
                            <p class="text-secondary">{{ __('24/7 priority support with a dedicated account manager, SLA guarantees, and on-call engineering team.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="400">
                        <div class="feature-card card border-0 shadow-sm h-100 p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-plug text-danger"></i>
                            </div>
                            <h5 class="fw-bold">{{ __('Custom Integrations') }}</h5>
                            <p class="text-secondary">{{ __('Seamless integration with your existing systems including CRM, ERP, HRIS, and custom business applications.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="500">
                        <div class="feature-card card border-0 shadow-sm h-100 p-4">
                            <div class="feature-icon mb-3">
                                <i class="fas fa-database text-purple" data-view-style="enterprise-9"></i>
                            </div>
                            <h5 class="fw-bold">{{ __('White-Label Solutions') }}</h5>
                            <p class="text-secondary">{{ __('Fully customizable platform with your branding, custom domain, and tailored user experience for your business.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-section py-5 bg-primary text-white">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8" data-aos="fade-right">
                        <h2 class="display-5 fw-bold">{{ __('Ready to Scale Your Business?') }}</h2>
                        <p class="fs-5 opacity-75">
                            {{ __('Get a custom enterprise solution tailored to your needs.') }}
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end" data-aos="fade-left">
                        <a href="{{ route('resources.show', 'live-chat') }}" class="btn btn-light rounded-pill px-5 py-3 fw-bold">
                            <i class="fas fa-arrow-right me-2"></i>{{ __('Contact Sales') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section class="faq-section py-5">
            <div class="container">
                <div class="text-center mb-5" data-aos="fade-down">
                    <h2 class="display-5 fw-bold">{{ __('Frequently Asked Questions') }}</h2>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item border-0 shadow-sm mb-3 rounded-3" data-aos="fade-up" data-aos-delay="0">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed rounded-3" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#faq0">
                                        {{ __('What is the minimum commitment for enterprise?') }}
                                    </button>
                                </h2>
                                <div id="faq0" class="accordion-collapse collapse"
                                        data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-secondary">
                                        {{ __('We offer flexible annual contracts with options for multi-year commitments for better pricing.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border-0 shadow-sm mb-3 rounded-3" data-aos="fade-up" data-aos-delay="100">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed rounded-3" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#faq1">
                                        {{ __('Can we customize the platform?') }}
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse"
                                        data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-secondary">
                                        {{ __('Yes! Enterprise plans include full customization including branding, workflows, and custom features.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border-0 shadow-sm mb-3 rounded-3" data-aos="fade-up" data-aos-delay="200">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed rounded-3" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#faq2">
                                        {{ __('What security certifications do you have?') }}
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse"
                                        data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-secondary">
                                        {{ __('We are SOC 2 Type II certified, GDPR compliant, and offer HIPAA compliance for healthcare businesses.') }}
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border-0 shadow-sm mb-3 rounded-3" data-aos="fade-up" data-aos-delay="300">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed rounded-3" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#faq3">
                                        {{ __('Do you offer on-premise deployment?') }}
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse"
                                        data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-secondary">
                                        {{ __('Yes, we offer hybrid and on-premise deployment options for enterprises with strict data requirements.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

@push('styles')
    @vite('resources/css/features.css')
@endpush
