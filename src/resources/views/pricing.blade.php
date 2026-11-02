@extends('layouts.app')
@section('style-page', 'pricing')

@section('title', __('Pricing'))

@section('content')
    <x-marketing-hero eyebrow="CLEAR PLANS. THOUGHTFUL TOOLS." title="{{ __('Find your rhythm.') }}" accent="Choose your plan." description="Start with the essentials and add more possibilities when you need them. Compare the plans below and choose what fits your day." icon="fa-gem" tone="purple">
        <x-slot:actions><a href="#booking-plans" class="btn btn-primary">{{ __('Compare plans') }} <i class="fas fa-arrow-down"></i></a><a href="{{ route('resources.show', 'live-chat') }}" class="btn btn-outline-primary">{{ __('Ask a question') }}</a></x-slot:actions>
    </x-marketing-hero>
    <div class="pricing-page py-5" id="booking-plans">
        <div class="container">

            <div class="row g-4 justify-content-center align-items-stretch">
                @foreach($plans as $plan)
                    <div class="col-md-6 col-xl-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="pricing-card card h-100 border-0 shadow-sm position-relative {{ $plan['popular'] ? 'popular' : '' }}">
                            @if($plan['popular'])
                                <div class="popular-badge">
                                    <i class="fas fa-star me-1"></i>{{ __('Most Popular') }}
                                </div>
                            @endif

                            <div class="card-body text-center p-4">
                                <h3 class="fw-bold text-primary">{{ __($plan['name']) }}</h3>
                                <p class="text-secondary small">{{ __($plan['description']) }}</p>

                                <div class="price-wrapper my-4">
                                    @if($plan['price'] == 0)
                                        <span class="display-4 fw-bold">{{ __('Free') }}</span>
                                    @else
                                        <span class="display-4 fw-bold">${{ $plan['price'] }}</span>
                                        <span class="text-secondary">/{{ __($plan['period']) }}</span>
                                    @endif
                                </div>

                                <ul class="list-unstyled text-start mb-4">
                                    @foreach($plan['features'] as $feature)
                                        <li class="mb-2">
                                            <i class="fas fa-check-circle text-primary me-2"></i>
                                            {{ __($feature) }}
                                        </li>
                                    @endforeach
                                </ul>

                                @if($plan['id'] === 'basic')
                                    <a href="{{ route('register') }}"
                                            class="btn {{ $plan['button_class'] }} rounded-pill px-5 w-100">
                                        {{ __($plan['button_text']) }}
                                    </a>

                                @elseif($plan['id'] === 'pro')
                                    @auth
                                        <a href="{{ route('booking.subscription.checkout.page', ['package' => 'pro']) }}"
                                                class="btn {{ $plan['button_class'] }} rounded-pill px-5 w-100">
                                            {{ __($plan['button_text']) }}
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}?redirect={{ urlencode(route('booking.subscription.checkout.page', ['package' => 'pro'])) }}"
                                                class="btn {{ $plan['button_class'] }} rounded-pill px-5 w-100">
                                            <i class="fas fa-lock me-2"></i>{{ __('Login to Subscribe') }}
                                        </a>
                                    @endauth

                                @elseif($plan['id'] === 'business')
                                    @auth
                                        <a href="{{ route('booking.subscription.checkout.page', ['package' => 'business']) }}"
                                                class="btn {{ $plan['button_class'] }} rounded-pill px-5 w-100">
                                            {{ __($plan['button_text']) }}
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}?redirect={{ urlencode(route('booking.subscription.checkout.page', ['package' => 'business'])) }}"
                                                class="btn {{ $plan['button_class'] }} rounded-pill px-5 w-100">
                                            <i class="fas fa-lock me-2"></i>{{ __('Login to Subscribe') }}
                                        </a>
                                    @endauth

                                @else
                                    <a href="{{ $plan['price'] == 0 ? (auth()->check() ? route('bookings.create') : route('register')) : route('resources.show', 'live-chat') }}" class="btn {{ $plan['button_class'] }} rounded-pill px-5 w-100">
                                        {{ __($plan['button_text']) }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row mt-5 pt-4">
                <div class="col-12 text-center">
                    <p class="text-secondary">
                        <i class="fas fa-shield-alt text-primary me-2"></i>
                        {{ __('Looking for security packages?') }}
                        <a href="{{ route('security') }}" class="text-primary fw-bold">{{ __('Check our Security Packages →') }}</a>
                    </p>
                </div>
            </div>

            <div class="row mt-5 pt-5">
                <div class="col-12 text-center mb-4">
                    <h2 class="fw-bold">{{ __('Frequently Asked Questions') }}</h2>
                    <p class="text-secondary">{{ __('Find answers to common questions') }}</p>
                </div>
                <div class="col-lg-8 mx-auto">
                    <div class="accordion" id="faqAccordion">
                        @foreach($faqs as $index => $faq)
                            <div class="accordion-item border-0 shadow-sm mb-3 rounded-3">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed rounded-3" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#faq{{ $index }}">
                                        {{ __($faq['question']) }}
                                    </button>
                                </h2>
                                <div id="faq{{ $index }}" class="accordion-collapse collapse"
                                        data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-secondary">
                                        {{ __($faq['answer']) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="row mt-5 pt-4">
                <div class="col-12 text-center">
                    <p class="text-secondary">{{ __('Have questions?') }} <a href="{{ route('resources.show', 'live-chat') }}" class="text-primary fw-bold">{{ __('Contact our team') }}</a></p>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('styles')
    @vite('resources/css/subscription.css')
@endpush
