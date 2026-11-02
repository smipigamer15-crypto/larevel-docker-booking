@extends('layouts.app')
@section('style-page', 'resources/about')

@section('title', __('About Us'))

@section('content')
    <div class="about-page marketing-page">

        <x-marketing-hero eyebrow="THE PEOPLE BEHIND THE PLATFORM" title="{{ __('A better day starts') }}" accent="with a simpler booking." description="We believe good software should give you time back. We're building a calmer, more connected way to manage appointments and help businesses grow." icon="fa-heart">
            <x-slot:actions><a href="{{ route('features') }}" class="btn btn-primary">{{ __('Explore our tools') }} <i class="fas fa-arrow-right"></i></a><a href="{{ route('register') }}" class="btn btn-outline-primary">{{ __('Join us') }}</a></x-slot:actions>
        </x-marketing-hero>

        <section class="py-5 bg-white border-top border-bottom border-light">
            <div class="container">
                <div class="row text-center g-4" data-aos="fade-up">
                    <div class="col-6 col-md-3">
                        <h2 class="display-4 fw-bold text-primary mb-1">500+</h2>
                        <p class="text-secondary fw-semibold mb-0">{{ __('Happy Businesses') }}</p>
                    </div>
                    <div class="col-6 col-md-3">
                        <h2 class="display-4 fw-bold text-primary mb-1">1M+</h2>
                        <p class="text-secondary fw-semibold mb-0">{{ __('Bookings Made') }}</p>
                    </div>
                    <div class="col-6 col-md-3">
                        <h2 class="display-4 fw-bold text-primary mb-1">99.9%</h2>
                        <p class="text-secondary fw-semibold mb-0">{{ __('Uptime Guarantee') }}</p>
                    </div>
                    <div class="col-6 col-md-3">
                        <h2 class="display-4 fw-bold text-primary mb-1">24/7</h2>
                        <p class="text-secondary fw-semibold mb-0">{{ __('Customer Support') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container py-5">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6" data-aos="fade-right">
                        <div class="position-relative">
                            <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80"
                                alt="{{ __('Our Team') }}"
                                class="img-fluid rounded-4 shadow-lg">
                            <div class="position-absolute bottom-0 end-0 bg-primary text-white p-4 rounded-4 shadow-lg m-3 d-none d-md-block" data-view-style="resources-about-3">
                                <h4 class="fw-bold mb-0">{{ __('Since 2020') }}</h4>
                                <p class="mb-0 opacity-75 small">{{ __('Helping you grow') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" data-aos="fade-left">
                        <span class="section-eyebrow text-primary fw-bold text-uppercase small" data-view-style="resources-about-4">{{ __('Our Mission') }}</span>
                        <h2 class="display-5 fw-bold mb-4 mt-2">{{ __('More time for what matters most') }}</h2>
                        <p class="text-secondary fs-5 mb-4">
                            {{ __('We started Booking because we saw talented professionals spending hours on the phone and managing spreadsheets instead of doing what they love.') }}
                        </p>
                        <p class="text-secondary mb-4">
                            {{ __('Our platform was built from the ground up to be intuitive, reliable, and beautiful. We handle the technical side of scheduling, payments, and security, so you can focus on your clients.') }}
                        </p>
                        <ul class="list-unstyled">
                            <li class="d-flex align-items-center gap-3 mb-3">
                                <i class="fas fa-check-circle text-success fs-5"></i>
                                <span class="fw-semibold text-dark">{{ __('No more double bookings') }}</span>
                            </li>
                            <li class="d-flex align-items-center gap-3 mb-3">
                                <i class="fas fa-check-circle text-success fs-5"></i>
                                <span class="fw-semibold text-dark">{{ __('Automated reminders for clients') }}</span>
                            </li>
                            <li class="d-flex align-items-center gap-3">
                                <i class="fas fa-check-circle text-success fs-5"></i>
                                <span class="fw-semibold text-dark">{{ __('Enterprise-grade security') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5 bg-light">
            <div class="container py-5">
                <div class="text-center mb-5" data-aos="fade-up">
                    <h2 class="display-5 fw-bold">{{ __('What drives us') }}</h2>
                    <p class="text-secondary fs-5">{{ __('Our core values shape everything we do.') }}</p>
                </div>

                <div class="row g-4">
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 text-center hover-lift bg-white">
                            <div class="value-icon mx-auto mb-4 bg-primary-soft text-primary">
                                <i class="fas fa-heart fs-4"></i>
                            </div>
                            <h5 class="fw-bold">{{ __('Customer First') }}</h5>
                            <p class="text-secondary mb-0">{{ __('We listen to our users and build the features they actually need to succeed.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 text-center hover-lift bg-white">
                            <div class="value-icon mx-auto mb-4 bg-success-soft text-success">
                                <i class="fas fa-shield-alt fs-4"></i>
                            </div>
                            <h5 class="fw-bold">{{ __('Trust & Security') }}</h5>
                            <p class="text-secondary mb-0">{{ __('Your data and your clients\' data are protected with the highest security standards.') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 text-center hover-lift bg-white">
                            <div class="value-icon mx-auto mb-4 bg-warning-soft text-warning">
                                <i class="fas fa-bolt fs-4"></i>
                            </div>
                            <h5 class="fw-bold">{{ __('Simplicity') }}</h5>
                            <p class="text-secondary mb-0">{{ __('Software should be easy to use. We remove complexity so you can work faster.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-section py-5 text-white">
            <div class="container py-5 text-center" data-aos="zoom-in">
                <h2 class="display-5 fw-bold mb-3">{{ __('Ready to grow your business?') }}</h2>
                <p class="fs-5 mb-4 opacity-75">{{ __('Join hundreds of businesses that trust us with their bookings.') }}</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('register') }}" class="btn btn-light rounded-pill px-5 py-3 fw-bold text-primary">
                        {{ __('Get Started Free') }}
                    </a>
                    <a href="{{ route('features') }}" class="btn btn-outline-light rounded-pill px-5 py-3 fw-bold">
                        {{ __('Explore Features') }}
                    </a>
                </div>
            </div>
        </section>
    </div>

@endsection
@push('styles')
    @vite('resources/css/resources.css')
@endpush
