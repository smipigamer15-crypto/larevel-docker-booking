@extends('layouts.app')
@section('style-page', 'bookings/index')

@section('title', __('My Bookings'))

@section('content')
    <div class="bookings-page py-5">
        <div class="container">
            <div class="bookings-header text-center mb-5" data-aos="fade-down">
                <h1 class="fw-bold section-title">{{ __('My Bookings') }}</h1>
                <p class="text-secondary" data-view-style="bookings-index-1">
                    {{ __('Manage all your appointments in one place') }}
                </p>
            </div>

            <div class="bookings-tabs-wrapper mb-4">
                <ul class="nav nav-tabs bookings-tabs" id="bookingsTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#upcoming">
                            <i class="fas fa-clock me-2"></i>
                            {{ __('Upcoming') }}
                            <span class="tab-badge">{{ $upcoming->count() }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#completed">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ __('Completed') }}
                            <span class="tab-badge">{{ $completed->count() }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#cancelled">
                            <i class="fas fa-times-circle me-2"></i>
                            {{ __('Cancelled') }}
                            <span class="tab-badge">{{ $cancelled->count() }}</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="upcoming">
                    @forelse($upcoming as $booking)
                        <div class="booking-card card-modern p-4 mb-3" data-aos="fade-up">
                            <div class="booking-list-row">
                                <div class="booking-list-info">
                                    <div class="booking-info">
                                        <h6 class="fw-bold mb-1">{{ $booking->service->title }}</h6>
                                        <div class="booking-meta">
                                            <span class="text-secondary small">
                                                <i class="far fa-calendar me-1"></i>
                                                {{ $booking->booking_date->format('d.m.Y') }}
                                            </span>
                                            <span class="text-secondary small ms-3">
                                                <i class="far fa-clock me-1"></i>
                                                {{ $booking->start_time }} - {{ $booking->end_time }}
                                            </span>
                                        </div>
                                        <div class="booking-service-info mt-2">
                                            <span class="text-secondary small">
                                                <i class="fas fa-tag me-1"></i>
                                                {{ $booking->service->category->name ?? __('General') }}
                                            </span>
                                            <span class="text-secondary small ms-3">
                                                <i class="fas fa-coins me-1"></i>
                                                {{ number_format($booking->service->price, 2) }} $
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="booking-list-status">
                                    <span class="booking-status status-upcoming">
                                        <i class="fas fa-clock me-1"></i>{{ __('Upcoming') }}
                                    </span>
                                </div>
                                <div class="booking-list-actions">
                                    <div class="booking-card-actions">
                                        <a href="{{ route('bookings.show', $booking) }}" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                            <i class="fas fa-eye me-1"></i>{{ __('Details') }}
                                        </a>
                                        <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-4" data-view-action="bookings-index-1" >
                                                <i class="fas fa-times me-1"></i>{{ __('Cancel') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state text-center py-5" data-aos="fade-up">
                            <div class="empty-icon">
                                <i class="fas fa-calendar-plus"></i>
                            </div>
                            <h4 class="fw-bold mt-3">{{ __('No upcoming bookings') }}</h4>
                            <p class="text-secondary">{{ __('You don\'t have any upcoming appointments') }}</p>
                            <a href="{{ route('bookings.create') }}" class="btn btn-primary rounded-pill mt-3 px-5">
                                <i class="fas fa-plus me-2"></i>{{ __('Book Now') }}
                            </a>
                        </div>
                    @endforelse
                </div>

                <div class="tab-pane fade" id="completed">
                    @forelse($completed as $booking)
                        <div class="booking-card card-modern p-4 mb-3" data-aos="fade-up">
                            <div class="booking-list-row">
                                <div class="booking-list-info">
                                    <div class="booking-info">
                                        <h6 class="fw-bold mb-1">{{ $booking->service->title }}</h6>
                                        <div class="booking-meta">
                                            <span class="text-secondary small">
                                                <i class="far fa-calendar me-1"></i>
                                                {{ $booking->booking_date->format('d.m.Y') }}
                                            </span>
                                            <span class="text-secondary small ms-3">
                                                <i class="far fa-clock me-1"></i>
                                                {{ $booking->start_time }} - {{ $booking->end_time }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="booking-list-status">
                                    <span class="booking-status status-completed">
                                        <i class="fas fa-check-circle me-1"></i>{{ __('Completed') }}
                                    </span>
                                </div>
                                <div class="booking-list-actions booking-card-actions">
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                        <i class="fas fa-eye me-1"></i>{{ __('Details') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state text-center py-5" data-aos="fade-up">
                            <div class="empty-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h4 class="fw-bold mt-3">{{ __('No completed bookings') }}</h4>
                            <p class="text-secondary">{{ __('Your completed appointments will appear here') }}</p>
                        </div>
                    @endforelse
                </div>

                <div class="tab-pane fade" id="cancelled">
                    @forelse($cancelled as $booking)
                        <div class="booking-card card-modern p-4 mb-3" data-aos="fade-up">
                            <div class="booking-list-row">
                                <div class="booking-list-info">
                                    <div class="booking-info">
                                        <h6 class="fw-bold mb-1">{{ $booking->service->title }}</h6>
                                        <div class="booking-meta">
                                            <span class="text-secondary small">
                                                <i class="far fa-calendar me-1"></i>
                                                {{ $booking->booking_date->format('d.m.Y') }}
                                            </span>
                                            <span class="text-secondary small ms-3">
                                                <i class="far fa-clock me-1"></i>
                                                {{ $booking->start_time }} - {{ $booking->end_time }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="booking-list-status">
                                    <span class="booking-status status-cancelled">
                                        <i class="fas fa-times-circle me-1"></i>{{ __('Cancelled') }}
                                    </span>
                                </div>
                                <div class="booking-list-actions booking-card-actions">
                                    <a href="{{ route('bookings.show', $booking) }}" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                        <i class="fas fa-eye me-1"></i>{{ __('Details') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state text-center py-5" data-aos="fade-up">
                            <div class="empty-icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <h4 class="fw-bold mt-3">{{ __('No cancelled bookings') }}</h4>
                            <p class="text-secondary">{{ __('You don\'t have any cancelled appointments') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
<input type="hidden" data-page-script="bookings/index">

@push('styles')
    @vite('resources/css/bookings.css')
@endpush
