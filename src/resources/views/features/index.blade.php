@extends('layouts.app')
@section('style-page', 'features/index')

@section('title', __('Features'))

@section('content')
    <div class="features-page">

        <section class="features-hero py-5">
            <div class="container">
                <div class="text-center" data-aos="fade-down">
                    <h1 class="display-3 fw-bold hero-title">
                        {{ __('All') }} <span class="text-primary">{{ __('Features') }}</span>
                    </h1>
                    <p class="hero-text mx-auto fs-5 text-secondary" data-view-style="features-index-1">
                        {{ __('Explore the tools designed to grow your business and manage bookings effortlessly.') }}
                    </p>
                </div>
            </div>
        </section>

        <section class="category-section sticky-filter-bar">
            <div class="container">
                <div class="d-flex flex-wrap justify-content-center gap-2" data-aos="fade-up">
                    <button class="btn btn-primary rounded-pill px-4 filter-btn active" data-filter="all">
                        {{ __('All Features') }}
                    </button>
                    <button class="btn btn-outline-primary rounded-pill px-4 filter-btn" data-filter="features">
                        {{ __('Features') }}
                    </button>
                    <button class="btn btn-outline-primary rounded-pill px-4 filter-btn" data-filter="integrations">
                        {{ __('Integrations') }}
                    </button>
                    <button class="btn btn-outline-primary rounded-pill px-4 filter-btn" data-filter="settings">
                        {{ __('Settings') }}
                    </button>
                    <button class="btn btn-outline-primary rounded-pill px-4 filter-btn" data-filter="extra">
                        {{ __('Extra') }}
                    </button>
                </div>
            </div>
        </section>

        <section class="features-grid-section py-5">
            <div class="container">
                <div class="features-grid">

                    @foreach($features as $slug => $feature)
                        <div class="feature-item" data-category="{{ $feature['category'] }}">
                            <a href="{{ $slug === 'security' ? route('security') : route('features.show', $slug) }}" class="feature-card-link text-decoration-none">
                                <div class="feature-card h-100 d-flex flex-column align-items-start p-4 bg-white rounded-4 border-0 shadow-sm">
                                    <div class="feature-icon mb-4"><i class="{{ str_starts_with($feature['icon'], 'fab') ? $feature['icon'] : 'fas '.$feature['icon'] }} text-primary"></i></div>
                                    <div class="flex-grow-1 mb-3"><h5 class="fw-bold text-dark mb-2">{{ __($feature['title']) }}</h5><p class="text-secondary small mb-0">{{ __($feature['description']) }}</p></div>
                                    <div class="mt-auto pt-3 border-top w-100"><span class="text-primary fw-semibold small">{{ __('Open feature') }} <i class="fas fa-arrow-right ms-2"></i></span></div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="cta-section py-5 bg-primary text-white mt-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8" data-aos="fade-right">
                        <h2 class="display-5 fw-bold">{{ __('Ready to Get Started?') }}</h2>
                        <p class="fs-5 opacity-75 mb-0">
                            {{ __('Create an account to book appointments and explore the available tools.') }}
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0" data-aos="fade-left">
                        <a href="{{ route('register') }}" class="btn btn-light rounded-pill px-5 py-3 fw-bold text-primary">
                            <i class="fas fa-rocket me-2"></i>{{ __('Create an account') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <input type="hidden" data-page-script="features/index">
@endsection
@push('styles')
    @vite('resources/css/features.css')
@endpush
