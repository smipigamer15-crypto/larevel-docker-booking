@extends('layouts.app')
@section('style-page', 'errors/403')

@section('title', __('Access Denied'))

@section('content')
    <div class="error-page py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="error-card">
                        <div class="error-icon-wrap" data-view-style="errors-403-1">
                            <i class="fas fa-shield-alt"></i>
                        </div>

                        <h1 class="error-code">403</h1>
                        <h2 class="error-title">{{ __('Access Denied') }}</h2>

                        <p class="error-message">
                            {{ __('You don\'t have permission to access this resource. If you think this is a mistake, please contact support.') }}
                        </p>

                        <div class="error-actions">
                            <a href="{{ url('/') }}" class="btn-primary-modern">
                                <i class="fas fa-home me-2"></i>{{ __('Home') }}
                            </a>
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn-clear">
                                    <i class="fas fa-user"></i>{{ __('Dashboard') }}
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn-clear">
                                    <i class="fas fa-sign-in-alt"></i>{{ __('Login') }}
                                </a>
                            @endauth
                        </div>

                        <div class="error-help">
                            <i class="fas fa-shield-virus me-1"></i>
                            {{ __('Error code: 403 · This incident has been logged.') }}
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
