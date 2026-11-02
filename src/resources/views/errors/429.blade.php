@extends('layouts.app')
@section('style-page', 'errors/429')

@section('title', __('Too Many Requests'))

@section('content')
    <div class="error-page py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="error-card">
                        <div class="error-icon-wrap" data-view-style="errors-429-1">
                            <i class="fas fa-hourglass-half"></i>
                        </div>

                        <h1 class="error-code">429</h1>
                        <h2 class="error-title">{{ __('Too Many Requests') }}</h2>

                        <p class="error-message">
                            {{ __('You\'ve made too many requests in a short period of time. Please wait a moment before trying again.') }}
                        </p>

                        <div class="error-timer">
                            <i class="fas fa-clock me-2"></i>
                            {{ __('Try again in') }} <strong id="countdown">60</strong> {{ __('seconds') }}
                        </div>

                        <div class="error-actions">
                            <a href="{{ url()->previous() }}" class="btn btn-primary-modern">
                                <i class="fas fa-arrow-left me-2"></i>{{ __('Go Back') }}
                            </a>
                            <a href="{{ url('/') }}" class="btn-clear">
                                <i class="fas fa-home"></i>{{ __('Home') }}
                            </a>
                        </div>

                        <div class="error-help">
                            <i class="fas fa-info-circle me-1"></i>
                            {{ __('If you believe this is a mistake, please contact support.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" data-page-script="errors/429">
@endsection
@push('styles')
    @vite('resources/css/errors.css')
@endpush
