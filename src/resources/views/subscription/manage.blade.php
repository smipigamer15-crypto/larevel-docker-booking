@extends('layouts.app')

@section('title', __('Manage Subscription'))

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg rounded-4 border-0">
                    <div class="card-body p-5">
                        <h1 class="fw-bold mb-4">{{ __('Manage Subscription') }}</h1>

                        @if(session('success'))
                            <div class="alert alert-success rounded-pill">{{ __(session('success')) }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger rounded-pill">{{ __(session('error')) }}</div>
                        @endif

                        <div class="mb-4">
                            <h5>{{ __('Current Plan:') }} <span class="text-primary">{{ $inquiry->package_label }}</span></h5>
                            <p>{{ __('Status:') }}
                                <span class="badge bg-{{ $inquiry->subscription_status === 'active' ? 'success' : 'warning' }}">
                                    {{ __(ucfirst($inquiry->subscription_status ?? 'N/A')) }}
                                </span>
                            </p>
                            @if($inquiry->subscription_ends_at)
                                <p>{{ __('Next billing date:') }} <strong>{{ $inquiry->subscription_ends_at->translatedFormat('M d, Y') }}</strong></p>
                            @endif
                            @if($inquiry->cancel_at_period_end)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    {{ __('Your subscription will be cancelled on') }} {{ $inquiry->subscription_ends_at->translatedFormat('M d, Y') }}.
                                </div>
                                <form method="POST" action="{{ route('subscription.resume', $inquiry->access_token) }}">
                                    @csrf
                                    <button class="btn btn-success rounded-pill px-4">{{ __('Resume Subscription') }}</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('subscription.cancel.subscription', $inquiry->access_token) }}"
                                        data-view-action="subscription-manage-1" >
                                    @csrf
                                    <button class="btn btn-outline-danger rounded-pill px-4">{{ __('Cancel Subscription') }}</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    @vite('resources/css/subscription.css')
@endpush
<input type="hidden" data-page-script="subscription/manage">
