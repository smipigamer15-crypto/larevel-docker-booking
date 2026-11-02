@extends('layouts.app')

@section('title', __('Booking Details'))

@section('content')
    <div class="booking-details-page py-5">
        <div class="container">
            <nav aria-label="{{ __('breadcrumb') }}" class="mb-4" data-aos="fade-down">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('bookings.index') }}">{{ __('My Bookings') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ __('Booking #') }}{{ $booking->id }}</li>
                </ol>
            </nav>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="booking-details-card" data-aos="fade-up">
                        <div class="booking-header">
                            <div class="booking-header-left">
                                <span class="booking-badge">
                                    <i class="fas fa-receipt me-2"></i>{{ __('Booking Details') }}
                                </span>
                                <h1 class="booking-title">{{ __('Booking #') }}{{ $booking->id }}</h1>
                                <p class="booking-date">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    {{ $booking->created_at->format('d.m.Y H:i') }}
                                </p>
                            </div>
                            <div class="booking-header-right">
                                <span class="booking-status
                                        @if($booking->status == 'upcoming') status-upcoming
                                        @elseif($booking->status == 'completed') status-completed
                                        @else status-cancelled
                                        @endif">
                                    <span class="status-dot"></span>
                                    {{ __(ucfirst($booking->status)) }}
                                </span>
                            </div>
                        </div>

                        <div class="booking-divider"></div>

                        <div class="service-info-wrapper">
                            <div class="service-info-card">
                                <div class="service-info-row">
                                    <div class="service-info-left">
                                        <span class="service-label">
                                            <i class="fas fa-concierge-bell"></i> {{ __('Service') }}
                                        </span>
                                        <h3 class="service-title">{{ $booking->service->title }}</h3>
                                        <div class="service-tags">
                                            <span class="service-tag">
                                                <i class="fas fa-tag"></i>
                                                {{ $booking->service->category->name ?? __('General') }}
                                            </span>
                                            <span class="service-tag">
                                                <i class="fas fa-clock"></i>
                                                {{ $booking->service->duration }} {{ __('min') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="service-info-right">
                                        <span class="price-label">{{ __('Total Price') }}</span>
                                        <div class="price-amount">
                                            <span class="booking-price-symbol">$</span>{{ number_format($booking->service->price, 2) }}
                                        </div>
                                        <span class="price-note">{{ __('Including all taxes') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="booking-details-grid">
                            <div class="details-row">
                                <div class="detail-card">
                                    <div class="detail-icon">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                    <div class="detail-content">
                                        <span class="detail-label">{{ __('Date') }}</span>
                                        <span class="detail-value">
                                            {{ $booking->booking_date->translatedFormat('l, d F Y') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="detail-card">
                                    <div class="detail-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="detail-content">
                                        <span class="detail-label">{{ __('Time') }}</span>
                                        <span class="detail-value">
                                            {{ $booking->start_time }} — {{ $booking->end_time }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="details-row">
                                <div class="detail-card">
                                    <div class="detail-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="detail-content">
                                        <span class="detail-label">{{ __('Booked by') }}</span>
                                        <span class="detail-value">{{ $booking->user->name }}</span>
                                        <span class="detail-sub">{{ $booking->user->email }}</span>
                                    </div>
                                </div>
                                <div class="detail-card">
                                    <div class="detail-icon">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="detail-content">
                                        <span class="detail-label">{{ __('Booking Date') }}</span>
                                        <span class="detail-value">
                                            {{ $booking->created_at->format('d.m.Y H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($booking->notes)
                            <div class="notes-wrapper">
                                <div class="notes-header">
                                    <i class="fas fa-pen"></i>
                                    <span>{{ __('Notes') }}</span>
                                </div>
                                <div class="notes-content">
                                    {{ $booking->notes }}
                                </div>
                            </div>
                        @endif

                        <div class="booking-divider"></div>

                        <div class="booking-actions">
                            <div class="actions-grid">
                                <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                                </a>
                                @if($booking->status == 'upcoming' && $booking->booking_date >= date('Y-m-d'))
                                    <form action="{{ route('bookings.cancel', $booking) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-danger" data-view-action="bookings-show-1" >
                                            <i class="fas fa-times"></i> {{ __('Cancel') }}
                                        </button>
                                    </form>
                                @endif
                                @if($booking->status == 'upcoming')
                                    <a href="#" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> {{ __('Reschedule') }}
                                    </a>
                                @endif
                                @if($booking->status == 'completed' && !$booking->review)
                                    <a href="{{ route('reviews.create', $booking) }}" class="btn btn-warning">
                                        <i class="fas fa-star"></i> {{ __('Write Review') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" data-page-script="bookings/show">
@endsection
@push('styles')
    @vite('resources/css/bookings.css')
@endpush
