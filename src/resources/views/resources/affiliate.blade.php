@extends('layouts.app')
@section('style-page', 'resources/affiliate')

@section('title', __('Affiliate Program'))

@section('content')
    <div class="affiliate-page marketing-page">
        <x-marketing-hero eyebrow="THE AFFILIATE PROGRAM" title="{{ __('Share something good.') }}" accent="Grow together." description="Introduce your audience to a simpler booking experience. Explore our affiliate program and talk to the team about getting started." icon="fa-share-alt" tone="amber">
            <x-slot:actions><a href="{{ route('resources.show', 'live-chat') }}" class="btn btn-primary">{{ __('Join the conversation') }} <i class="fas fa-arrow-right"></i></a><a href="#how-it-works" class="btn btn-outline-primary">{{ __('How it works') }}</a></x-slot:actions>
        </x-marketing-hero>
        <div class="container py-4">

            <div class="row g-4 text-center mt-5 mb-5" data-aos="fade-up">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 hover-lift">
                        <div class="display-5 fw-bold text-primary mb-2">30%</div>
                        <h5 class="fw-bold">{{ __('Recurring Commission') }}</h5>
                        <p class="text-secondary small mb-0">{{ __('Earn 30% of the monthly revenue from every customer you refer, for as long as they stay subscribed.') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 hover-lift">
                        <div class="display-5 fw-bold text-primary mb-2">90</div>
                        <h5 class="fw-bold">{{ __('Day Cookie Window') }}</h5>
                        <p class="text-secondary small mb-0">{{ __('You get credit for referrals up to 90 days after they click your unique affiliate link.') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100 hover-lift">
                        <div class="display-5 fw-bold text-primary mb-2">$50</div>
                        <h5 class="fw-bold">{{ __('Min. Payout') }}</h5>
                        <p class="text-secondary small mb-0">{{ __('Cash out your earnings via PayPal or bank transfer once you reach the $50 threshold.') }}</p>
                    </div>
                </div>
            </div>

            <div id="how-it-works" class="py-5 mt-4 border-top border-light" data-aos="fade-up">
                <div class="text-center mb-5">
                    <h2 class="display-5 fw-bold">{{ __('How it works') }}</h2>
                    <p class="text-secondary fs-5">{{ __('Start earning in 3 simple steps') }}</p>
                </div>
                <div class="row g-4 justify-content-center">
                    <div class="col-md-4">
                        <div class="d-flex align-items-start gap-3 p-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5 flex-shrink-0 shadow-sm" data-view-style="resources-affiliate-4">1</div>
                            <div>
                                <h5 class="fw-bold mt-1">{{ __('Sign Up') }}</h5>
                                <p class="text-secondary small mb-0">{{ __('Register for the affiliate program and get your unique tracking link instantly.') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-start gap-3 p-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5 flex-shrink-0 shadow-sm" data-view-style="resources-affiliate-5">2</div>
                            <div>
                                <h5 class="fw-bold mt-1">{{ __('Share') }}</h5>
                                <p class="text-secondary small mb-0">{{ __('Share your link on your blog, social media, or with your audience and clients.') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-start gap-3 p-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5 flex-shrink-0 shadow-sm" data-view-style="resources-affiliate-6">3</div>
                            <div>
                                <h5 class="fw-bold mt-1">{{ __('Earn') }}</h5>
                                <p class="text-secondary small mb-0">{{ __('Earn recurring commissions every month your referred users stay active.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5 pt-4" data-aos="zoom-in">
                <div class="card marketing-cta-card border-0 shadow-lg rounded-4 p-5" data-view-style="resources-affiliate-7">
                    <h2 class="text-white fw-bold mb-3">{{ __('Ready to start earning?') }}</h2>
                    <p class="text-white-50 fs-5 mb-4">{{ __('Join hundreds of affiliates who are already earning passive income.') }}</p>
                    <div>
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg rounded-pill px-5 fw-bold text-primary shadow">
                            {{ __('Create Affiliate Account') }}
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
@push('styles')
    @vite('resources/css/resources.css')
@endpush
