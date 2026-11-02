@extends('layouts.app')
@section('style-page', 'errors/404')

@section('title', __('Page Not Found'))

@section('content')
    <div class="error-page py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="error-card">
                        <div class="error-icon-wrap" data-view-style="errors-404-1">
                            <i class="fas fa-compass"></i>
                        </div>

                        <h1 class="error-code">404</h1>
                        <h2 class="error-title">{{ __('Page Not Found') }}</h2>

                        <p class="error-message">
                            {{ __('The page you\'re looking for doesn\'t exist or has been moved. Let\'s get you back on track.') }}
                        </p>

                        <div class="error-actions">
                            <a href="{{ url('/') }}" class="btn-primary-modern">
                                <i class="fas fa-home me-2"></i>{{ __('Home') }}
                            </a>
                            <a href="{{ url()->previous() }}" class="btn-clear">
                                <i class="fas fa-arrow-left"></i>{{ __('Go Back') }}
                            </a>
                        </div>

                        <div class="error-help">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ __('Error code: 404 · If this keeps happening, please contact support.') }}
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
