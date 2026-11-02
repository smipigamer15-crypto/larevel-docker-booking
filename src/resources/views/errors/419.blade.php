@extends('layouts.app')
@section('style-page', 'errors/419')

@section('title', __('Session Expired'))

@section('content')
    <div class="error-page py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="error-card">
                        <div class="error-icon-wrap" data-view-style="errors-419-1">
                            <i class="fas fa-clock"></i>
                        </div>

                        <h1 class="error-code">419</h1>
                        <h2 class="error-title">{{ __('Session Expired') }}</h2>

                        <p class="error-message">
                            {{ __('Your session has expired for security reasons. Please refresh the page and try again.') }}
                        </p>

                        <div class="error-actions">
                            <a href="{{ url()->previous() }}" class="btn-primary-modern">
                                <i class="fas fa-redo me-2"></i>{{ __('Refresh Page') }}
                            </a>
                            <a href="{{ url('/') }}" class="btn-clear">
                                <i class="fas fa-home"></i>{{ __('Home') }}
                            </a>
                        </div>

                        <div class="error-help">
                            <i class="fas fa-shield-alt me-1"></i>
                            {{ __('Error code: 419 · This is a normal security measure.') }}
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
