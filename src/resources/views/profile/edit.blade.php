@extends('layouts.app')
@section('style-page', 'profile/edit')
@section('title', __('My Account'))
@section('content')
    @php
        $status = session('status');
        $hasSecurityErrors = $errors->getBag('updatePassword')->any() || $errors->getBag('userDeletion')->any();
        $initialSection = '';

        if ($hasSecurityErrors || $status === 'password-updated') {
            $initialSection = 'security';
        } elseif ($status === 'api-token-generated') {
            $initialSection = 'workspace';
        } elseif ($status === 'profile-updated' || $status === 'avatar-removed') {
            $initialSection = 'account';
        }

        $profileSections = [
            'account' => [__('Personal details'), 'user-edit'],
            'billing' => [__('Plans & billing'), 'credit-card'],
            'workspace' => [__('Workspace & tools'), 'sliders-h'],
            'security' => [__('Password & security'), 'shield-alt'],
        ];
    @endphp

    <div class="profile-page" data-profile-workspace data-profile-initial="{{ $initialSection }}">
        <section class="profile-hero">
            <div class="container">
                <div class="d-flex flex-wrap align-items-end justify-content-between gap-4">
                    <div>
                        <span class="profile-eyebrow">{{ __('YOUR PERSONAL WORKSPACE') }}</span>
                        <h1>{{ __('Everything in') }} <span>{{ __('one place.') }}</span></h1>
                        <p>{{ __('Manage your details, plans and the tools that work for you.') }}</p>
                    </div>
                    <a class="btn btn-outline-primary" href="{{ route('bookings.index') }}">
                        <i class="far fa-calendar-check me-2"></i>{{ __('My bookings') }}
                    </a>
                </div>

                <div class="profile-overview">
                    <div>
                        <span class="profile-overview-icon"><i class="fas fa-user-check"></i></span>
                        <div>
                            <small>{{ __('Account status') }}</small>
                            <strong>{{ $user->hasVerifiedEmail() ? __('Email verified') : __('Verify your email') }}</strong>
                            @unless($user->hasVerifiedEmail())
                                <a href="{{ route('verification.notice') }}">{{ __('Complete verification') }}</a>
                            @endunless
                        </div>
                    </div>
                    <div>
                        <span class="profile-overview-icon purple"><i class="fas fa-crown"></i></span>
                        <div>
                            <small>{{ __('Booking plan') }}</small>
                            <strong>{{ $user->activeBookingSubscription()?->plan_label ?? __('Free') }}</strong>
                        </div>
                    </div>
                    <div>
                        <span class="profile-overview-icon green"><i class="far fa-calendar"></i></span>
                        <div>
                            <small>{{ __('Bookings remaining this month') }}</small>
                            <strong>{{ $user->bookingsRemaining() ?? __('Unlimited') }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="container profile-body">
            @if(session('success'))
                <div class="alert alert-success" role="status">{{ __(session('success')) }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger" role="alert">{{ __(session('error')) }}</div>
            @endif
            @if($status === 'api-token-generated')
                <div class="alert alert-success" role="status">{{ __('Your API token has been generated.') }}</div>
            @endif
            @if($errors->any() || $hasSecurityErrors)
                <div class="alert alert-danger" role="alert">{{ __('Some details need your attention. Review the marked fields below.') }}</div>
            @endif

            <div class="profile-layout">
                <aside class="profile-sidebar">
                    <div class="profile-identity">
                        <div class="profile-avatar">
                            @if($user->avatar)
                                <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}">
                            @else
                                <span class="avatar-initials">{{ mb_strtoupper(mb_substr($user->name, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div>
                            <h2>{{ $user->name }}</h2>
                            <p>{{ $user->email }}</p>
                            <span class="user-role-badge">{{ $user->isAdmin() ? __('Administrator') : __('Member') }}</span>
                        </div>
                    </div>
                    <nav class="profile-section-nav" aria-label="{{ __('Account sections') }}" data-profile-tablist>
                        @foreach($profileSections as $section => [$label, $icon])
                            <a href="#{{ $section }}" id="profile-tab-{{ $section }}" class="profile-nav-item" data-profile-tab="{{ $section }}">
                                <i class="fas fa-{{ $icon }}"></i>
                                <span>{{ $label }}</span>
                                <i class="fas fa-chevron-right profile-tab-arrow"></i>
                            </a>
                        @endforeach
                    </nav>
                    <div class="profile-shortcuts">
                        <a href="{{ route('dashboard') }}"><i class="fas fa-chart-pie"></i> {{ __('Open dashboard') }}</a>
                        <a href="{{ route('resources.show', 'live-chat') }}"><i class="far fa-comment-dots"></i> {{ __('Contact support') }}</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"><i class="fas fa-sign-out-alt"></i> {{ __('Sign out') }}</button>
                        </form>
                    </div>
                </aside>
                <div class="profile-panels">
                    <section id="account" data-profile-panel="account" aria-labelledby="profile-tab-account">
                        @include('profile.partials.account')
                    </section>
                    <section id="billing" data-profile-panel="billing" aria-labelledby="profile-tab-billing">
                        @include('profile.partials.billing')
                    </section>
                    <section id="workspace" data-profile-panel="workspace" aria-labelledby="profile-tab-workspace">
                        @include('profile.partials.workspace')
                    </section>
                    <section id="security" data-profile-panel="security" aria-labelledby="profile-tab-security">
                        @include('profile.partials.security')
                    </section>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" data-page-script="profile/edit">
@endsection
@push('styles')
    @vite('resources/css/profile.css')
@endpush
