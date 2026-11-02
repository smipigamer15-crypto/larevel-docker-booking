@extends('layouts.app')
@section('style-page', 'subscription/success')

@section('title', __('Payment Successful'))

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-lg rounded-4 text-center p-5">
                    <div class="success-icon mb-4">
                        <i class="fas fa-check-circle text-success" data-view-style="subscription-success-1"></i>
                    </div>
                    <h1 class="fw-bold mb-3">{{ __('Payment Successful!') }}</h1>
                    <p class="text-secondary mb-4">
                        {{ __('Thank you for subscribing to') }} <strong>{{ $inquiry->package_label }}</strong>
                    </p>

                    <div class="alert alert-success rounded-3">
                        <p class="mb-1"><strong>{{ __('Status:') }}</strong> {{ __(ucfirst($inquiry->subscription_status ?? __('Active'))) }}</p>
                        <p class="mb-0"><strong>{{ __('Next billing:') }}</strong> {{ $inquiry->subscription_ends_at?->translatedFormat('M d, Y') }}</p>
                    </div>

                    <a href="{{ route('subscription.manage', $inquiry->access_token) }}" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-cog me-2"></i>{{ __('Manage Subscription') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    @vite('resources/css/subscription.css')
@endpush
