@extends('layouts.app')
@section('style-page', 'feature-tool')
@section('title', $settings['business_name'])
@section('content')
    <section class="feature-public-page py-5" data-template="{{ $settings['template'] }}" data-brand-accent="{{ $settings['accent'] }}">
        <div class="container py-4">
            <div class="feature-brand-preview mb-5"><span class="feature-eyebrow">{{ __('BOOK ONLINE') }}</span><h1 class="display-4 fw-bold">{{ $settings['business_name'] }}</h1><p class="lead">{{ $settings['welcome'] }}</p></div>
            <div class="row g-4">@forelse($services as $service)<div class="col-md-6 col-xl-4"><div class="feature-tool-panel h-100"><span class="feature-eyebrow">{{ $service->duration }} {{ __('MINUTES') }}</span><h2 class="h4 mt-3">{{ $service->title }}</h2><p class="text-secondary">{{ Str::limit($service->description, 150) }}</p><div class="d-flex justify-content-between align-items-center mt-4"><strong>${{ number_format($service->price, 2) }}</strong><a href="{{ route('bookings.create', ['service_id' => $service->id]) }}" class="btn btn-primary">{{ __('Choose a time') }}</a></div></div></div>@empty<p class="text-secondary">{{ __('No services are currently available.') }}</p>@endforelse</div>
        </div>
    </section>
@endsection
@push('styles')
    @vite('resources/css/features.css')
@endpush
