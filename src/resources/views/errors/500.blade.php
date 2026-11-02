@extends('layouts.app')
@section('style-page', 'errors/500')

@section('title', __('Server Error'))

@section('content')
    <div class="error-page py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="error-card">
                        <div class="error-icon-wrap" data-view-style="errors-500-1">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>

                        <h1 class="error-code">500</h1>
                        <h2 class="error-title">{{ __('Something Went Wrong') }}</h2>

                        <p class="error-message">
                            {{ __('We\'re experiencing technical difficulties. Our team has been notified and is working on it.') }}
                        </p>

                        <div class="error-actions">
                            <a href="{{ url('/') }}" class="btn-primary-modern">
                                <i class="fas fa-home me-2"></i>{{ __('Home') }}
                            </a>
                            <a href="{{ url()->previous() }}" class="btn-clear">
                                <i class="fas fa-redo"></i>{{ __('Try Again') }}
                            </a>
                        </div>

                        <div class="error-help">
                            <i class="fas fa-bug me-1"></i>
                            {{ __('Error code: 500 · Please try again in a few moments.') }}
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
