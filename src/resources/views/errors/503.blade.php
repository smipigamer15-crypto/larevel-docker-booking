@extends('layouts.app')
@section('style-page', 'errors/503')

@section('title', __('Maintenance Mode'))

@section('content')
    <div class="error-page py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="error-card">
                        <div class="error-icon-wrap" data-view-style="errors-503-1">
                            <i class="fas fa-tools"></i>
                        </div>

                        <h1 class="error-code">503</h1>
                        <h2 class="error-title">{{ __('Under Maintenance') }}</h2>

                        <p class="error-message">
                            {{ __('We\'re currently performing scheduled maintenance. We\'ll be back shortly. Thank you for your patience!') }}
                        </p>

                        <div class="error-actions">
                            <a href="{{ url('/') }}" class="btn-primary-modern">
                                <i class="fas fa-redo me-2"></i>{{ __('Retry') }}
                            </a>
                        </div>

                        <div class="error-help">
                            <i class="fas fa-clock me-1"></i>
                            {{ __('We\'ll be back online soon · Thank you for your patience!') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('styles')
    @vite('resources/css/errors.css')
@endpush
