@extends('layouts.app')
@section('style-page', 'features/security')

@section('title', __('Security'))

@section('content')
    <div class="security-page marketing-page">
        <x-marketing-hero eyebrow="SECURITY, WITHOUT THE COMPLEXITY" title="{{ __('We protect what') }}" accent="matters most." description="Build trust with thoughtful security tools for your account, your data and your everyday bookings." icon="fa-shield-alt">
            <x-slot:actions><a href="#contact" class="btn btn-primary">{{ __('Secure your business') }} <i class="fas fa-arrow-right"></i></a><a href="#packages" class="btn btn-outline-primary">{{ __('Explore security plans') }}</a></x-slot:actions>
            <x-slot:visual><div class="security-stats-card bg-white p-4 rounded-4 shadow-lg">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <div class="stat-box p-3 rounded-3" data-view-style="features-security-2">
                                <h2 class="display-5 fw-bold text-primary mb-0">99.9%</h2>
                                <p class="text-secondary small mb-0">{{ __('Uptime Guarantee') }}</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box p-3 rounded-3" data-view-style="features-security-3">
                                <h2 class="display-5 fw-bold text-success mb-0">{{ __('256-bit') }}</h2>
                                <p class="text-secondary small mb-0">{{ __('SSL Encryption') }}</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box p-3 rounded-3" data-view-style="features-security-4">
                                <h2 class="display-5 fw-bold text-warning mb-0">24/7</h2>
                                <p class="text-secondary small mb-0">{{ __('Monitoring') }}</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box p-3 rounded-3" data-view-style="features-security-5">
                                <h2 class="display-5 fw-bold text-danger mb-0">{{ __('ISO') }}</h2>
                                <p class="text-secondary small mb-0">{{ __('27001 Certified') }}</p>
                            </div>
                        </div>
                    </div>
            </div></x-slot:visual>
        </x-marketing-hero>

        <section id="packages" class="packages-section py-5 bg-light">
            <div class="container">
                <div class="text-center mb-5" data-aos="fade-down">
                    <h2 class="display-5 fw-bold">{{ __('Choose Your') }} <span class="text-primary">{{ __('Security Plan') }}</span></h2>
                    <p class="text-secondary fs-5">{{ __('Protect your business with our security packages') }}</p>
                </div>

                <div class="row g-4">
                    @php
                        $packages = [
                            [
                                'id' => 'basic',
                                'name' => __('Basic Security'),
                                'price' => 0,
                                'period' => 'month',
                                'description' => __('Essential security for small businesses'),
                                'features' => [
                                    __('SSL Encryption (256-bit)'),
                                    __('Basic Firewall'),
                                    __('Email Support'),
                                ],
                                'popular' => false,
                                'button_text' => __('Get Started'),
                                'button_class' => 'btn-outline-primary'
                            ],
                            [
                                'id' => 'pro',
                                'name' => __('Pro Security'),
                                'price' => 49,
                                'period' => 'month',
                                'description' => __('Advanced security for growing businesses'),
                                'features' => [
                                    __('SSL Encryption (256-bit)'),
                                    '2FA Authentication',
                                    '24/7 Monitoring',
                                    __('Daily Backups'),
                                    __('DDoS Protection'),
                                    __('Vulnerability Scan'),
                                ],
                                'popular' => true,
                                'button_text' => __('Subscribe Now'),
                                'button_class' => 'btn-primary'
                            ],
                            [
                                'id' => 'enterprise',
                                'name' => __('Enterprise Security'),
                                'price' => 199,
                                'period' => 'month',
                                'description' => __('Complete security for large enterprises'),
                                'features' => [
                                    __('SSL Encryption (256-bit)'),
                                    __('All Pro Security features'),
                                    __('Advanced Firewall'),
                                    __('Priority Support'),
                                    __('API Access'),
                                    __('Custom Security Rules'),
                                    __('Dedicated Security Team'),
                                ],
                                'popular' => false,
                                'button_text' => __('Subscribe Now'),
                                'button_class' => 'btn-outline-primary'
                            ],
                            [
                                'id' => 'custom',
                                'name' => __('Custom Security'),
                                'price' => __('Custom'),
                                'period' => 'month',
                                'description' => __('Tailored security solution for your needs'),
                                'features' => [
                                    __('SSL Encryption (256-bit)'),
                                    '2FA Authentication',
                                    '24/7 Monitoring',
                                    __('Daily Backups'),
                                    __('Custom Configuration'),
                                    __('Dedicated Support'),
                                ],
                                'popular' => false,
                                'button_text' => __('Contact Us'),
                                'button_class' => 'btn-outline-primary'
                            ]
                        ];
                    @endphp

                    @foreach($packages as $package)
                        <div class="col-md-3" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                            <div class="package-card card border-0 shadow-sm h-100 p-4 text-center {{ $package['popular'] ? 'popular' : '' }}">
                                @if($package['popular'])
                                    <div class="popular-badge">{{ __('Most Popular') }}</div>
                                @endif

                                <div class="security-icon mb-2">
                                    <i class="fas fa-shield-alt text-primary"></i>
                                </div>

                                <h4 class="fw-bold">{{ $package['name'] }}</h4>
                                <p class="text-secondary small">{{ $package['description'] }}</p>

                                <div class="package-price my-3">
                                    @if($package['price'] === 0)
                                        <span class="display-6 fw-bold text-primary">{{ __('Free') }}</span>
                                    @elseif($package['price'] === __('Custom'))
                                        <span class="display-6 fw-bold text-primary">{{ __('Custom') }}</span>
                                    @else
                                        <span class="display-6 fw-bold text-primary">${{ $package['price'] }}</span>
                                        <span class="text-secondary">/{{ $package['period'] }}</span>
                                    @endif
                                </div>

                                <ul class="list-unstyled text-start mb-3">
                                    @foreach($package['features'] as $feature)
                                        <li class="mb-1 small">
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>

                                @if($package['id'] === 'basic' || $package['id'] === 'custom')
                                    <a href="#contact" class="btn {{ $package['button_class'] }} rounded-pill px-4 mt-auto w-100">
                                        {{ $package['button_text'] }}
                                    </a>
                                @else
                                    @auth
                                        <a href="{{ route('security.checkout', ['package' => $package['id']]) }}"
                                                class="btn {{ $package['button_class'] }} rounded-pill px-4 mt-auto w-100">
                                            {{ $package['button_text'] }}
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}?redirect={{ urlencode(route('security.checkout', ['package' => $package['id']])) }}"
                                                class="btn {{ $package['button_class'] }} rounded-pill px-4 mt-auto w-100">
                                            <i class="fas fa-lock me-2"></i>{{ __('Login to Subscribe') }}
                                        </a>
                                    @endauth
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="features-section py-5">
            <div class="container">
                <div class="text-center mb-5" data-aos="fade-down">
                    <h2 class="display-5 fw-bold">{{ __('Security') }} <span class="text-primary">{{ __('Features') }}</span></h2>
                    <p class="text-secondary fs-5">{{ __('Comprehensive security measures to protect your business') }}</p>
                </div>

                <div class="row g-4">
                    @php
                        $securityFeatures = [
                            ['icon' => 'fa-lock', 'title' => __('End-to-End Encryption'), 'description' => __('All data is encrypted in transit using TLS 1.3 and at rest using AES-256 encryption standards.'), 'color' => 'primary'],
                            ['icon' => 'fa-user-shield', 'title' => __('Role-Based Access Control'), 'description' => __('Granular permissions for different user roles. Admin, Manager, Staff, and Client levels with custom permissions.'), 'color' => 'success'],
                            ['icon' => 'fa-shield-virus', 'title' => __('DDoS Protection'), 'description' => __('Advanced DDoS protection with rate limiting, IP blocking, and automatic threat detection to keep your service online.'), 'color' => 'info'],
                            ['icon' => 'fa-history', 'title' => __('Audit Logs'), 'description' => __('Complete audit trail of all actions. Track who did what and when with detailed activity logs.'), 'color' => 'warning'],
                            ['icon' => 'fa-fingerprint', 'title' => __('Two-Factor Authentication'), 'description' => __('Add an extra layer of security with 2FA. SMS, email, or authenticator app options for all users.'), 'color' => 'danger'],
                            ['icon' => 'fa-database', 'title' => __('Data Backup & Recovery'), 'description' => __('Automated daily backups with point-in-time recovery. Your data is always safe and recoverable.'), 'color' => 'purple'],
                        ];
                    @endphp
                    @foreach($securityFeatures as $feature)
                        <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 50 }}">
                            <div class="feature-card card border-0 shadow-sm h-100 p-4 text-center">
                                <div class="feature-icon mx-auto mb-3">
                                    <i class="fas {{ $feature['icon'] }} text-{{ $feature['color'] }}"></i>
                                </div>
                                <h5 class="fw-bold">{{ __($feature['title']) }}</h5>
                                <p class="text-secondary small">{{ __($feature['description']) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="contact" class="contact-simple py-5 bg-light">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center" data-aos="fade-down">
                        <h2 class="display-5 fw-bold text-dark">{{ __('Ready to') }} <span class="text-primary">{{ __('Secure Your Business?') }}</span></h2>
                        <p class="text-secondary fs-5">{{ __('Get started with a free security assessment') }}</p>
                    </div>
                </div>
                <div class="row justify-content-center mt-4">
                    <div class="col-lg-8" data-aos="fade-up">
                        <div class="contact-card bg-white p-4 p-md-5 rounded-4 shadow-sm">
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
                            <form method="POST" action="{{ route('security.store') }}">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">{{ __('Full Name') }}</label>
                                        <input type="text" class="form-control form-input" name="name"
                                            placeholder="{{ __('John Doe') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">{{ __('Email Address') }}</label>
                                        <input type="email" class="form-control form-input" name="email"
                                            placeholder="john@example.com" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">{{ __('Company Name') }}</label>
                                        <input type="text" class="form-control form-input" name="company"
                                            placeholder="{{ __('Your Company') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-dark">{{ __('Package') }}</label>
                                        <select class="form-select form-input" name="package" required>
                                            <option value="">{{ __('Select a package...') }}</option>
                                            <option value="basic">{{ __('Basic Security (Free)') }}</option>
                                            <option value="pro">{{ __('Pro Security ($49/mo)') }}</option>
                                            <option value="enterprise">{{ __('Enterprise Security ($199/mo)') }}</option>
                                            <option value="custom">{{ __('Custom Security') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold text-dark">{{ __('Message') }}</label>
                                        <textarea class="form-control form-input" name="message" rows="3"
                                            placeholder="{{ __('Tell us about your security needs...') }}"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 w-100">
                                            <i class="fas fa-shield-alt me-2"></i>{{ __('Get Free Security Assessment') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-section py-5 bg-primary text-white">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8" data-aos="fade-right">
                        <h2 class="display-5 fw-bold">{{ __('Need Help with Security?') }}</h2>
                        <p class="fs-5 opacity-75">
                            {{ __('Our security experts are ready to help you 24/7.') }}
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end" data-aos="fade-left">
                        <a href="mailto:support@booking.com" class="btn btn-light rounded-pill px-5 py-3 fw-bold">
                            <i class="fas fa-headset me-2"></i>{{ __('Contact Support') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection
@push('styles')
    @vite('resources/css/features.css')
@endpush
