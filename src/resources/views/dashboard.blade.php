@extends('layouts.app')
@section('style-page', 'dashboard')

@section('title', __('Dashboard'))

@section('content')
    <div class="dashboard-page py-5">
        <div class="container">
            <div class="dashboard-header mb-5" data-aos="fade-down">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <h1 class="fw-bold section-title mb-1">{{ __('Welcome back,') }} {{ auth()->user()->name }}!</h1>
                        <p class="text-secondary">{{ __('Here\'s what\'s happening with your account') }}</p>
                    </div>
                    <div>
                        <a href="{{ route('bookings.create') }}" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-plus me-2"></i>{{ __('New Booking') }}
                        </a>
                    </div>
                </div>
            </div>

            @php
                $user = auth()->user();
                $activeSubscription = $user->activeBookingSubscription();
                $planName = $activeSubscription ? $activeSubscription->plan_label : __('Basic');
                $bookingLimit = $user->monthlyBookingLimit();
                $bookingsUsed = $user->bookingsThisMonth();
                $bookingsRemaining = $user->bookingsRemaining();
                $hasUnlimited = $bookingLimit === null;
                $usagePercent = $hasUnlimited ? 0 : ($bookingLimit > 0 ? min(100, ($bookingsUsed / $bookingLimit) * 100) : 0);
            @endphp

            <div class="dashboard-stats row g-4 mb-5">
                <div class="col-md-3 col-sm-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-card">
                        <div class="stat-icon" data-view-style="dashboard-1">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number">{{ $totalBookings }}</span>
                            <span class="stat-label">{{ __('Total Bookings') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-card">
                        <div class="stat-icon" data-view-style="dashboard-2">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number">{{ $todayBookings }}</span>
                            <span class="stat-label">{{ __('Today\'s Bookings') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-card">
                        <div class="stat-icon" data-view-style="dashboard-3">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number">{{ $upcomingBookings }}</span>
                            <span class="stat-label">{{ __('Upcoming Bookings') }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-card">
                        <div class="stat-icon" data-view-style="dashboard-4">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number">{{ $completedBookings }}</span>
                            <span class="stat-label">{{ __('Completed Bookings') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            @if(auth()->user()->hasActiveBookingSubscription())
                <div class="row g-4 mb-5" data-aos="fade-up">
                    <div class="col-12">
                        <div class="dashboard-card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-chart-line text-primary me-2"></i>{{ __('Analytics & Reports') }}
                                </h5>
                            </div>

                            <div class="row g-4">
                                <div class="col-lg-8">
                                    <div class="p-3 bg-light rounded-4 h-100">
                                        <h6 class="fw-bold mb-3 text-secondary">{{ __('Bookings Overview (Last 6 Months)') }}</h6>
                                        <canvas id="bookingsChart" height="100"></canvas>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="p-3 bg-light rounded-4 h-100 text-center">
                                        <h6 class="fw-bold mb-3 text-secondary">{{ __('Top Services') }}</h6>
                                        <div data-view-style="dashboard-5">
                                            <canvas id="topServicesChart"></canvas>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="p-3 bg-light rounded-4">
                                        <h6 class="fw-bold mb-3 text-secondary">{{ __('Revenue (Last 6 Months)') }}</h6>
                                        <canvas id="revenueChart" height="60"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="view-data-dashboard-0" value="{{ json_encode($chartMonths) }}">
                <input type="hidden" id="view-data-dashboard-1" value="{{ json_encode($chartBookings) }}">
                <input type="hidden" id="view-data-dashboard-2" value="{{ json_encode($chartMonths) }}">
                <input type="hidden" id="view-data-dashboard-3" value="{{ json_encode($chartRevenue) }}">
                <input type="hidden" id="view-data-dashboard-4" value="{{ json_encode($topServices->map(fn($s) => $s->service->title ?? 'N/A')) }}">
                <input type="hidden" id="view-data-dashboard-5" value="{{ json_encode($topServices->pluck('total')) }}">
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <input type="hidden" data-page-script="dashboard">
            @else
                <div class="row mb-5" data-aos="fade-up">
                    <div class="col-12">
                        <div class="card shadow-sm border-0 rounded-4 p-5 text-center bg-light">
                            <div class="mb-3">
                                <i class="fas fa-lock fa-3x text-secondary opacity-50"></i>
                            </div>
                            <h4 class="fw-bold">{{ __('Analytics & Reports') }}</h4>
                            <p class="text-secondary mb-4">{{ __('Upgrade your plan to unlock powerful analytics, track your bookings, and see your revenue.') }}</p>
                            <div>
                                <a href="{{ route('pricing') }}" class="btn btn-primary rounded-pill px-4">
                                    <i class="fas fa-crown me-2"></i>{{ __('Upgrade to Pro') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="booking-limit-card mb-5" data-aos="fade-up" data-aos-delay="150">
                <div class="limit-header">
                    <div class="limit-title">
                        <div class="limit-icon {{ $hasUnlimited ? 'unlimited' : ($bookingsRemaining <= 2 ? 'danger' : 'normal') }}">
                            <i class="fas {{ $hasUnlimited ? 'fa-infinity' : 'fa-calendar-check' }}"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">
                                @if($hasUnlimited)
                                    {{ __('Unlimited Bookings') }}
                                @else
                                    {{ __('Monthly Booking Limit') }}
                                @endif
                            </h5>
                            <p class="text-secondary small mb-0">
                                @if($hasUnlimited)
                                    {{ __('Your') }} <strong>{{ $planName }}</strong> {{ __('plan includes unlimited bookings') }}
                                @else
                                    {{ __('Free plan — up to') }} <strong>{{ $bookingLimit }}</strong> {{ __('bookings per month') }}
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($hasUnlimited)
                        <span class="limit-badge unlimited">
                            <i class="fas fa-crown me-1"></i>{{ $planName }}
                        </span>
                    @else
                        <a href="{{ route('pricing') }}" class="btn btn-upgrade">
                            <i class="fas fa-arrow-up me-1"></i>{{ __('Upgrade') }}
                        </a>
                    @endif
                </div>

                @if(!$hasUnlimited)
                    <div class="limit-progress-wrapper">
                        <div class="limit-progress-info">
                            <span class="limit-used">
                                <strong>{{ $bookingsUsed }}</strong> {{ __('of') }} {{ $bookingLimit }} {{ __('used') }}
                            </span>
                            <span class="limit-remaining {{ $bookingsRemaining <= 2 ? 'text-danger' : '' }}">
                                <strong>{{ $bookingsRemaining }}</strong> {{ __('remaining') }}
                            </span>
                        </div>
                        <div class="limit-progress-bar">
                            <div class="limit-progress-fill
                                    @if($usagePercent >= 80) danger
                                    @elseif($usagePercent >= 50) warning
                                    @endif"
                                    data-view-style="dashboard-6" data-view-width="{{ $usagePercent }}">
                            </div>
                        </div>
                    </div>

                    @if($bookingsRemaining <= 2)
                        <div class="limit-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            @if($bookingsRemaining == 0)
                                {{ __('You\'ve reached your monthly limit.') }}
                            @else
                                {{ __('Only') }} <strong>{{ $bookingsRemaining }}</strong> {{ __('booking') }}{{ $bookingsRemaining > 1 ? 's' : '' }} {{ __('left this month.') }}
                            @endif
                            <a href="{{ route('pricing') }}" class="fw-bold ms-1">{{ __('Upgrade to Pro for unlimited →') }}</a>
                        </div>
                    @endif
                @else
                    <div class="limit-progress-wrapper">
                        <div class="limit-progress-info">
                            <span class="limit-used">
                                <strong>{{ $bookingsUsed }}</strong> {{ __('bookings this month') }}
                            </span>
                            <span class="limit-remaining text-success">
                                <i class="fas fa-check-circle me-1"></i>{{ __('No limits') }}
                            </span>
                        </div>
                        <div class="limit-progress-bar unlimited-bar">
                            <div class="limit-progress-fill unlimited-fill" data-view-style="dashboard-7"></div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="row g-4">
                <div class="col-lg-8" data-aos="fade-up" data-aos-delay="200">
                    <div class="dashboard-card">
                        <div class="card-header-custom">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-clock text-primary me-2"></i>{{ __('Recent Bookings') }}
                            </h5>
                            <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                {{ __('View All') }} <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="card-body-custom">
                            @if($recentBookings->count() > 0)
                                @foreach($recentBookings as $booking)
                                    <div class="recent-booking-item">
                                        <div class="recent-booking-info">
                                            <span class="fw-semibold">{{ $booking->service->title }}</span>
                                            <span class="text-secondary small d-block">
                                                <i class="far fa-calendar me-1"></i>{{ $booking->booking_date->format('d.m.Y') }}
                                                <i class="far fa-clock ms-2 me-1"></i>{{ $booking->start_time }}
                                            </span>
                                        </div>
                                        <span class="booking-status-badge
                                                @if($booking->status == 'upcoming') status-upcoming-badge
                                                @elseif($booking->status == 'completed') status-completed-badge
                                                @else status-cancelled-badge
                                                @endif">
                                            {{ __(ucfirst($booking->status)) }}
                                        </span>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-calendar-plus fa-3x text-muted opacity-25 mb-2 d-block"></i>
                                    <p class="text-secondary mb-0">{{ __('No bookings yet') }}</p>
                                    <a href="{{ route('bookings.create') }}" class="btn btn-primary rounded-pill mt-3">
                                        <i class="fas fa-plus me-2"></i>{{ __('Book Now') }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="dashboard-card">
                        <div class="card-header-custom">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-bolt text-warning me-2"></i>{{ __('Quick Actions') }}
                            </h5>
                        </div>
                        <div class="card-body-custom">
                            <div class="quick-actions-grid">
                                <a href="{{ route('bookings.create') }}" class="quick-action-item">
                                    <div class="quick-action-icon" data-view-style="dashboard-8">
                                        <i class="fas fa-calendar-plus"></i>
                                    </div>
                                    <span>{{ __('New Booking') }}</span>
                                </a>
                                <a href="{{ route('services.index') }}" class="quick-action-item">
                                    <div class="quick-action-icon" data-view-style="dashboard-9">
                                        <i class="fas fa-list"></i>
                                    </div>
                                    <span>{{ __('Browse Services') }}</span>
                                </a>
                                <a href="{{ route('profile.edit') }}" class="quick-action-item">
                                    <div class="quick-action-icon" data-view-style="dashboard-10">
                                        <i class="fas fa-user-edit"></i>
                                    </div>
                                    <span>{{ __('Edit Profile') }}</span>
                                </a>
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin') }}" class="quick-action-item">
                                        <div class="quick-action-icon" data-view-style="dashboard-11">
                                            <i class="fas fa-crown"></i>
                                        </div>
                                        <span>{{ __('Admin Panel') }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" data-page-script="dashboard-2">
@endsection
@push('styles')
    @vite('resources/css/dashboard.css')
@endpush
