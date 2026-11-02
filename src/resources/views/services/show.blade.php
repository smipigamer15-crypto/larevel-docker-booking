@extends('layouts.app')
@section('style-page', 'services/show')

@section('title', $service->title)

@section('content')
    <section class="service-details py-4 py-lg-5">
        <div class="container">
            <nav aria-label="{{ __('breadcrumb') }}" class="service-breadcrumb mb-4">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('services.index') }}">{{ __('Services') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $service->title }}</li>
                </ol>
            </nav>

            <div class="service-page">
                <main class="service-main">
                    <section class="service-hero mb-4">
                        @if($service->image && file_exists(public_path($service->image)))
                            <img
                                src="{{ asset($service->image) }}"
                                class="service-hero-image"
                                alt="{{ $service->title }}"
                                >
                        @else
                            <div class="service-hero-placeholder">
                                <i class="fas fa-calendar-check"></i>
                                <span>{{ $service->title }}</span>
                            </div>
                        @endif

                        <div class="service-hero-shade"></div>
                        <div class="service-hero-content">
                            <span class="badge rounded-pill category-badge mb-3">
                                {{ $service->category->name ?? __('General') }}
                            </span>
                            <h1 class="service-title">{{ $service->title }}</h1>

                            <div class="rating-wrapper">
                                <span class="rating-stars" aria-label="{{ round($averageRating) }} out of 5 stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($averageRating))
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </span>
                                <strong>{{ number_format($averageRating, 1) }}</strong>
                                <span class="rating-count">({{ $reviewsCount }} {{ __('reviews)') }}</span>
                            </div>
                        </div>
                    </section>

                    <section class="service-information h-100">
                        <div class="service-section-heading">
                            <div>
                                <span class="section-eyebrow">{{ __('SERVICE OVERVIEW') }}</span>
                                <h2>{{ __('Description') }}</h2>
                            </div>
                        </div>
                        <p class="service-description-text">{{ $service->description }}</p>

                        <div class="service-specs">
                            <article class="spec-card">
                                <span class="spec-icon"><i class="fas fa-clock"></i></span>
                                <div>
                                    <span class="spec-label">{{ __('Duration') }}</span>
                                    <strong>{{ $service->duration }} {{ __('minutes') }}</strong>
                                </div>
                            </article>

                            <article class="spec-card">
                                <span class="spec-icon"><i class="fas fa-tag"></i></span>
                                <div>
                                    <span class="spec-label">{{ __('Price') }}</span>
                                    <strong>{{ number_format($service->price, 2) }} $</strong>
                                </div>
                            </article>

                            <article class="spec-card">
                                <span class="spec-icon"><i class="fas fa-layer-group"></i></span>
                                <div>
                                    <span class="spec-label">{{ __('Category') }}</span>
                                    <strong>{{ $service->category->name ?? __('General') }}</strong>
                                </div>
                            </article>
                        </div>
                    </section>
                </main>

                <aside class="service-sidebar d-flex flex-column gap-4">
                    <section class="related-services">
                        <div class="sidebar-heading">
                            <span class="sidebar-heading-icon"><i class="fas fa-lightbulb"></i></span>
                            <div>
                                <span class="section-eyebrow">{{ __('DISCOVER MORE') }}</span>
                                <h2>{{ __('You might also like') }}</h2>
                            </div>
                        </div>

                        @if($relatedServices->count() > 0)
                            <div class="related-list">
                                @foreach($relatedServices as $related)
                                    <a href="{{ route('services.show', $related) }}" class="related-card">
                                        <div class="related-image">
                                            @if($related->image && file_exists(public_path($related->image)))
                                                <img src="{{ asset($related->image) }}" alt="{{ $related->title }}">
                                            @else
                                                <div class="related-placeholder">
                                                    <i class="fas fa-calendar-check"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="related-info">
                                            <h3 class="related-title">{{ $related->title }}</h3>
                                            <div class="related-meta">
                                                <strong class="related-price">{{ number_format($related->price, 2) }} $</strong>
                                                <span><i class="far fa-clock"></i> {{ $related->duration }} {{ __('min') }}</span>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-right related-arrow"></i>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-related">
                                <i class="fas fa-box-open"></i>
                                <p>{{ __('No related services found') }}</p>
                            </div>
                        @endif
                    </section>

                    <section class="booking-cta mt-auto">
                        <div class="booking-cta-icon"><i class="fas fa-calendar-check"></i></div>
                        <h2>{{ __('Ready to book?') }}</h2>
                        <p>{{ __('Choose a convenient time for your appointment.') }}</p>
                        <a href="{{ route('bookings.create', ['service_id' => $service->id]) }}" class="btn-book-now">
                            {{ __('Book Now') }} <i class="fas fa-arrow-right"></i>
                        </a>
                    </section>
                </aside>
            </div>
        </div>
    </section>

@endsection
@push('styles')
    @vite('resources/css/services.css')
@endpush
