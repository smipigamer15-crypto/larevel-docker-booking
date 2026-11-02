@extends('layouts.app')
@section('style-page', 'home')

@section('title', __('Book your appointment online'))

@section('content')

    <section class="hero-section position-relative overflow-hidden py-5" data-view-style="home-1">
        <div class="container position-relative" data-view-style="home-2">
            <div class="row align-items-center py-5">
                <div class="col-lg-6 mb-5 mb-lg-0" data-aos="fade-right" data-aos-delay="200">
                    <h1 class="display-3 fw-bold mb-4" data-view-style="home-3">
                        {{ __('Convenient') }} <br>
                        <span class="text-gradient" data-view-style="home-4">{{ __('planning') }}</span> <br>
                        {{ __('in advance') }}
                    </h1>
                    <p class="lead text-secondary mb-4" data-view-style="home-5">
                        {{ __('Join 20 million professionals who easily schedule meetings with the best planning tool.') }}
                    </p>
                    <div class="d-flex align-items-center gap-3 my-4" data-view-style="home-6">
                        <div class="flex-grow-1" data-view-style="home-7"></div>
                        <span class="text-secondary small fw-bold">{{ __('OR') }}</span>
                        <div class="flex-grow-1" data-view-style="home-8"></div>
                    </div>
                    <div class="mt-4 pt-3" data-aos="fade-up" data-aos-delay="300">
                        <p class="text-secondary small fw-semibold mb-3">
                            <i class="fas fa-building me-2 text-primary"></i>{{ __('Trusted by over 100,000 leading organizations worldwide') }}
                        </p>
                        <div class="d-flex gap-4 flex-wrap">
                            <span class="fw-bold text-dark"><i class="fas fa-crown me-2 text-warning"></i>{{ __('ACME Inc.') }}</span>
                            <span class="fw-bold text-dark"><i class="fas fa-rocket me-2 text-primary"></i>{{ __('Tech Corp') }}</span>
                            <span class="fw-bold text-dark"><i class="fas fa-globe me-2 text-success"></i>{{ __('Global Solutions') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-delay="400">
                    <div class="hero-slider-wrapper position-relative mx-auto" data-view-style="home-9">

                        <div class="slide-card active" data-slide="0">
                            <div class="card border-0 rounded-4 p-4 shadow-sm" data-view-style="home-10">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-semibold"><i class="fas fa-share-alt me-2 text-primary"></i>{{ __('Share your booking page') }}</span>
                                    <span class="badge rounded-pill bg-primary">{{ __('Popular') }}</span>
                                </div>
                                <div class="border-bottom pb-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="text-secondary small d-block"><i class="fas fa-link me-1"></i>{{ __('Your page') }}</span>
                                            <span class="fw-bold text-primary"><i class="fas fa-globe me-2"></i>{{ __('booking.com/your-page') }}</span>
                                        </div>
                                        <button class="btn btn-sm btn-primary rounded-pill px-3"><i class="fas fa-copy me-1"></i>{{ __('Copy') }}</button>
                                    </div>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="bg-light rounded-3 p-3">
                                            <span class="text-secondary d-block" data-view-style="home-11">{{ __('Select Date') }}</span>
                                            <span class="fw-bold small"><i class="fas fa-calendar-day me-1 text-primary"></i>{{ __('Jul 2024') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="bg-light rounded-3 p-3">
                                            <span class="text-secondary d-block" data-view-style="home-12">{{ __('Time zone') }}</span>
                                            <span class="fw-bold small"><i class="fas fa-globe-americas me-1 text-primary"></i>{{ __('Eastern time') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 d-flex gap-2 flex-wrap">
                                    <span class="bg-light px-3 py-1 rounded-pill small"><i class="far fa-clock me-1"></i>{{ __('10:00am') }}</span>
                                    <span class="bg-light px-3 py-1 rounded-pill small"><i class="far fa-clock me-1"></i>{{ __('11:00am') }}</span>
                                    <span class="text-white px-3 py-1 rounded-pill small" data-view-style="home-13"><i class="fas fa-check me-1"></i>{{ __('12:00pm') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="slide-card" data-slide="1">
                            <div class="card border-0 rounded-4 p-4 shadow-sm" data-view-style="home-14">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-semibold"><i class="fas fa-chart-line me-2 text-primary"></i>{{ __('Booking Analytics') }}</span>
                                    <span class="badge rounded-pill bg-success"><i class="fas fa-arrow-up me-1"></i>+12%</span>
                                </div>
                                <div class="text-center py-3">
                                    <div class="display-5 fw-bold text-primary"><i class="fas fa-calendar-check me-2"></i>1,847</div>
                                    <span class="text-secondary small"><i class="fas fa-users me-1"></i>{{ __('Total bookings') }}</span>
                                </div>
                                <div class="d-flex align-items-end gap-1 mt-3" data-view-style="home-15">
                                    <div class="flex-fill rounded-top" data-view-style="home-16"></div>
                                    <div class="flex-fill rounded-top" data-view-style="home-17"></div>
                                    <div class="flex-fill rounded-top" data-view-style="home-18"></div>
                                    <div class="flex-fill rounded-top" data-view-style="home-19"></div>
                                    <div class="flex-fill rounded-top" data-view-style="home-20"></div>
                                </div>
                            </div>
                        </div>

                        <div class="slide-card" data-slide="2">
                            <div class="card border-0 rounded-4 p-4 shadow-sm" data-view-style="home-21">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="fw-semibold"><i class="fas fa-star me-2 text-warning"></i>{{ __('Client Reviews') }}</span>
                                    <span class="text-warning fw-bold"><i class="fas fa-star"></i> 4.9 ★</span>
                                </div>
                                <div class="bg-light rounded-3 p-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center" data-view-style="home-22">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <span class="fw-semibold d-block"><i class="fas fa-user-check me-1 text-success"></i>{{ __('John Doe') }}</span>
                                            <span class="text-warning small"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></span>
                                        </div>
                                    </div>
                                    <p class="text-secondary small fst-italic mt-3 mb-0 border-start border-3 border-primary ps-2">
                                        <i class="fas fa-quote-left me-2 text-primary"></i>{{ __('"Great service! Very convenient for scheduling meetings."') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="slider-dots d-flex justify-content-center gap-2 mt-4">
                            <button type="button" class="dot active bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" data-slide="0" data-view-style="home-23" aria-label="{{ __('Show slide') }}"><i class="fas fa-share-alt"></i></button>
                            <button type="button" class="dot bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center" data-slide="1" data-view-style="home-24" aria-label="{{ __('Show slide') }}"><i class="fas fa-chart-line"></i></button>
                            <button type="button" class="dot bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center" data-slide="2" data-view-style="home-25" aria-label="{{ __('Show slide') }}"><i class="fas fa-star"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="popular-services py-5">
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
                <div>
                    <h2 class="section-title">{{ __('Popular Services') }}</h2>
                    <p class="text-secondary mb-0">{{ __('Most booked services by our customers') }}</p>
                </div>
                <a href="{{ route('services.index') }}" class="view-all-btn">
                    {{ __('View all') }} <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
            <div class="row g-4">
                @forelse($services as $service)
                    <div class="col-md-4">
                        <div class="service-card">
                            <div class="service-image-wrapper">
                                @if($service->image && file_exists(public_path($service->image)))
                                    <img src="{{ asset($service->image) }}" class="service-image" alt="{{ $service->title }}">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                        <i class="fas fa-calendar-check fa-4x text-primary" data-view-style="home-26"></i>
                                    </div>
                                @endif
                                <div class="service-overlay">
                                    <a href="{{ route('services.show', $service) }}" class="btn btn-light rounded-pill px-4">
                                        {{ __('View Details') }}
                                    </a>
                                </div>
                            </div>
                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="service-title mb-0">{{ $service->title }}</h5>
                                    <span class="category-badge">{{ $service->category->name ?? __('General') }}</span>
                                </div>
                                <p class="service-description">{{ Str::limit($service->description, 80) }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <div>
                                        <span class="service-price">{{ number_format($service->price, 2) }} $</span>
                                        <span class="service-duration ms-2"><i class="far fa-clock me-1"></i>{{ $service->duration }} {{ __('min') }}</span>
                                    </div>
                                    <a href="{{ route('bookings.create', ['service_id' => $service->id]) }}" class="book-btn">
                                        {{ __('Book now') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <h4 class="fw-bold text-secondary">{{ __('No services available yet') }}</h4>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="why-choose-us py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h2 class="fw-bold">{{ __('Why choose us') }}</h2>
                <p class="text-secondary fs-5">{{ __('We make booking simple and hassle-free') }}</p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                    <div class="benefit-card">
                        <div class="benefit-icon-wrapper mb-4">
                            <div class="benefit-icon">
                                <i class="fas fa-bolt"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold mb-2">{{ __('Quick & Easy') }}</h5>
                        <p class="text-secondary mb-0">{{ __('Book your appointment in just a few clicks') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="benefit-card">
                        <div class="benefit-icon-wrapper mb-4">
                            <div class="benefit-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold mb-2">{{ __('Secure Booking') }}</h5>
                        <p class="text-secondary mb-0">{{ __('Your data is safe and secure') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="benefit-card">
                        <div class="benefit-icon-wrapper mb-4">
                            <div class="benefit-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold mb-2">{{ __('24/7 Support') }}</h5>
                        <p class="text-secondary mb-0">{{ __('We\'re here to help you anytime') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="cta-card p-5 text-center text-white">
                <h2 class="fw-bold mb-3">{{ __('Ready to get started?') }}</h2>
                <p class="mb-4 text-white-50">{{ __('Join thousands of satisfied customers who book with us') }}</p>
                <div>
                    <a href="{{ route('register') }}" class="btn btn-light btn-lg rounded-pill px-5 text-primary fw-bold">
                        {{ __('Get Started') }} <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <input type="hidden" data-page-script="home">
@endpush
@push('styles')
    @vite('resources/css/home.css')
@endpush
