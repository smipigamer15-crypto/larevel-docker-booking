@extends('layouts.app')
@section('style-page', 'resources/partners')

@section('title', __('Partners'))

@section('content')
    <div class="partners-page marketing-page">

        <x-marketing-hero eyebrow="BETTER TOGETHER" title="{{ __('Good partnerships.') }}" accent="Greater possibilities." description="For agencies, technology providers and people who care about better booking experiences. Let's find a way to grow together." icon="fa-handshake" tone="green">
            <x-slot:actions><a href="{{ route('resources.show', 'live-chat') }}" class="btn btn-primary">{{ __('Start a conversation') }} <i class="fas fa-arrow-right"></i></a><a href="{{ route('features') }}" class="btn btn-outline-primary">{{ __('Discover the platform') }}</a></x-slot:actions>
        </x-marketing-hero>

        <section class="py-5 bg-white">
            <div class="container py-4">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6" data-aos="fade-right">

                        <div class="partner-graphic shadow-lg rounded-4 d-flex align-items-center justify-content-center position-relative">
                            <i class="fas fa-handshake text-white opacity-25 position-absolute" data-view-style="resources-partners-3"></i>
                            <div class="position-relative z-1 text-center">
                                <div class="d-flex justify-content-center gap-4 mb-3">
                                    <div class="icon-circle bg-white shadow-sm"><i class="fas fa-building text-primary"></i></div>
                                    <div class="icon-circle bg-white shadow-sm"><i class="fas fa-handshake text-primary"></i></div>
                                    <div class="icon-circle bg-white shadow-sm"><i class="fas fa-rocket text-primary"></i></div>
                                </div>
                                <h4 class="text-white fw-bold mb-0">{{ __('Stronger Together') }}</h4>
                            </div>
                        </div>

                    </div>
                    <div class="col-lg-6" data-aos="fade-left">
                        <h2 class="display-6 fw-bold mb-4 text-dark">{{ __('Why partner with us?') }}</h2>
                        <p class="text-secondary mb-4">
                            {{ __('By joining our partner network, you gain access to exclusive resources, dedicated support, and co-marketing opportunities.') }}
                        </p>
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex align-items-start gap-3 mb-4">
                                <div class="bg-primary-soft text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" data-view-style="resources-partners-4">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">{{ __('Revenue Growth') }}</h6>
                                    <p class="text-secondary small mb-0">{{ __('Offer our platform to your clients and earn attractive commissions.') }}</p>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-3 mb-4">
                                <div class="bg-primary-soft text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" data-view-style="resources-partners-5">
                                    <i class="fas fa-bullhorn"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">{{ __('Co-Marketing') }}</h6>
                                    <p class="text-secondary small mb-0">{{ __('Get featured in our blog, newsletters, and social media channels.') }}</p>
                                </div>
                            </li>
                            <li class="d-flex align-items-start gap-3">
                                <div class="bg-primary-soft text-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" data-view-style="resources-partners-6">
                                    <i class="fas fa-headset"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1 text-dark">{{ __('Dedicated Support') }}</h6>
                                    <p class="text-secondary small mb-0">{{ __('Get a dedicated account manager to help you succeed.') }}</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5 bg-light">
            <div class="container py-4 text-center" data-aos="zoom-in">
                <h2 class="display-6 fw-bold mb-3 text-dark">{{ __('Ready to become a partner?') }}</h2>
                <p class="text-secondary mb-4">{{ __('Fill out the form below and our partnership team will get back to you within 24 hours.') }}</p>
                <a href="{{ route('resources.show', 'live-chat') }}" class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow">
                    <i class="fas fa-envelope me-2"></i>{{ __('Contact Partnership Team') }}
                </a>
            </div>
        </section>
    </div>

@endsection
@push('styles')
    @vite('resources/css/resources.css')
@endpush
