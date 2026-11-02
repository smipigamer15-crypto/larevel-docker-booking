@extends('layouts.app')
@section('style-page', 'bookings/create')

@section('title', __('Book a Service'))

@section('content')
    <section class="booking-page">
        <div class="container">
            <div class="booking-wrapper">
                <div class="booking-intro text-center mb-4">
                    <span class="booking-eyebrow"><i class="far fa-calendar-check"></i> {{ __('A MOMENT FOR YOU') }}</span>
                    <h1 class="booking-heading">{{ __('Your next appointment,') }}<br><span>{{ __('beautifully simple.') }}</span></h1>
                    <p class="text-secondary mb-0">{{ __('Choose a service, find your time and we\'ll take care of the rest.') }}</p>
                </div>

                <div class="booking-card create-form">
                    <div class="booking-progress">
                        <div class="progress-steps" aria-label="{{ __('Booking progress') }}">
                            <div class="step active" data-step="1" aria-current="step">
                                <div class="step-number"><i class="fas fa-concierge-bell"></i></div>
                                <div class="step-label">{{ __('Service') }}</div>
                            </div>
                            <div class="step-line active"></div>
                            <div class="step" data-step="2">
                                <div class="step-number"><i class="fas fa-calendar-day"></i></div>
                                <div class="step-label">{{ __('Date & Time') }}</div>
                            </div>
                            <div class="step-line"></div>
                            <div class="step" data-step="3">
                                <div class="step-number"><i class="fas fa-check"></i></div>
                                <div class="step-label">{{ __('Confirm') }}</div>
                            </div>
                        </div>
                        <p id="booking-step-status" class="booking-step-status" aria-live="polite">{{ __('Step 1 of 3 · Choose your service') }}</p>
                    </div>

                    <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                        @csrf

                        @php
                            $remaining = auth()->user()->bookingsRemaining();
                            $limit = auth()->user()->monthlyBookingLimit();
                            $activeSubscription = auth()->user()->activeBookingSubscription();
                            $planName = $activeSubscription?->plan_label;
                        @endphp

                        @if($limit !== null)
                            <div class="limit-alert {{ $remaining <= 2 ? 'limit-warning' : 'limit-info' }}">
                                <div class="limit-icon-wrapper">
                                    <i class="fas {{ $remaining <= 2 ? 'fa-exclamation-triangle' : 'fa-calendar-check' }}"></i>
                                </div>
                                <div class="limit-content">
                                    <div class="limit-title">
                                        @if($remaining <= 2)
                                            {{ __('Running low on bookings') }}
                                        @else
                                            {{ __('Monthly Booking Limit') }}
                                        @endif
                                    </div>
                                    <div class="limit-text">
                                        {{ __('You have') }} <strong>{{ $remaining }}</strong> {{ __('of') }} <strong>{{ $limit }}</strong> {{ __('bookings remaining this month.') }}
                                        @if($remaining <= 2)
                                            <span class="ms-1">{{ __('Upgrade to Pro for unlimited bookings.') }}</span>
                                        @endif
                                    </div>
                                </div>
                                @if($remaining <= 2)
                                    <a href="{{ route('pricing') }}" class="limit-btn">
                                        {{ __('Upgrade') }} <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                @endif
                            </div>
                        @else
                            <div class="limit-alert limit-success">
                                <div class="limit-icon-wrapper">
                                    <i class="fas fa-crown"></i>
                                </div>
                                <div class="limit-content">
                                    <div class="limit-title">{{ __('Unlimited Access') }}</div>
                                    <div class="limit-text">
                                        {{ __('Your') }} <strong>{{ $planName ?? __('Pro') }}</strong> {{ __('plan is active. You can book as many services as you need.') }}
                                    </div>
                                </div>
                                @if($planName)
                                    <span class="limit-badge">
                                        {{ $planName }}
                                    </span>
                                @endif
                            </div>
                        @endif

                        <div class="booking-step active" data-step="1">
                            <div class="step-header">
                                <h4 class="fw-bold mb-1">{{ __('Select Your Service') }}</h4>
                                <p class="text-secondary small mb-0">{{ __('Choose the service you want to book') }}</p>
                            </div>

                            <div class="mb-4">
                                <label for="service_id" class="form-label">{{ __('Service') }}</label>
                                <div class="input-icon-wrapper">
                                    <i class="fas fa-concierge-bell input-icon"></i>
                                    <select class="form-select booking-input" id="service_id" name="service_id" required>
                                        <option value="">{{ __('Choose a service...') }}</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}"
                                                    data-price="{{ $service->price }}"
                                                    data-duration="{{ $service->duration }}"
                                                    data-name="{{ $service->title }}"
                                                    data-category="{{ $service->category->name ?? '' }}"
                                                    {{ $selectedService && $selectedService->id == $service->id ? 'selected' : '' }}>
                                                {{ $service->title }} — ${{ number_format($service->price, 2) }} ({{ $service->duration }} {{ __('min)') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('service_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="service-preview" id="servicePreview" data-view-style="bookings-create-1">
                                <div class="service-preview-card">
                                    <div class="service-preview-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="service-preview-info">
                                        <div class="fw-bold" id="previewTitle">{{ __('Service Name') }}</div>
                                        <div class="service-preview-meta">
                                            <span><i class="far fa-clock"></i><span id="previewDuration">0</span> {{ __('min') }}</span>
                                            <span><i class="fas fa-tag"></i>$<span id="previewPrice">0</span></span>
                                            <span id="previewCategory"><i class="fas fa-folder"></i><span id="previewCategoryName"></span></span>
                                        </div>
                                    </div>
                                    <span class="badge-selected">{{ __('Selected') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="booking-step" data-step="2" data-view-style="bookings-create-2">
                            <div class="step-header">
                                <h4 class="fw-bold mb-1">{{ __('Select Date & Time') }}</h4>
                                <p class="text-secondary small mb-0">{{ __('Choose a convenient date and time') }}</p>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="booking_date" class="form-label">
                                        <i class="far fa-calendar-alt me-1"></i>{{ __('Date') }}
                                    </label>
                                    <input type="date"
                                        class="form-control booking-input @error('booking_date') is-invalid @enderror"
                                        id="booking_date"
                                        name="booking_date"
                                        value="{{ old('booking_date', request('date')) }}"
                                        min="{{ date('Y-m-d') }}"
                                        required>
                                    @error('booking_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="start_time" class="form-label">
                                        <i class="far fa-clock me-1"></i>{{ __('Time') }}
                                    </label>
                                    <div class="input-icon-wrapper">
                                        <i class="fas fa-clock input-icon"></i>
                                        <select class="form-select booking-input" id="start_time" name="start_time" data-prefill-time="{{ old('start_time', request('time')) }}" required>
                                            <option value="">{{ __('Select time...') }}</option>
                                        </select>
                                    </div>
                                    @error('start_time')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="calendar-mini mt-4">
                                <div class="calendar-header">
                                    <span class="fw-bold" id="calendarMonth">{{ __('July 2024') }}</span>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="calendar-nav-btn" id="prevMonth" aria-label="{{ __('Previous month') }}">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <button type="button" class="calendar-nav-btn" id="nextMonth" aria-label="{{ __('Next month') }}">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="calendar-grid" id="calendarGrid"></div>
                            </div>
                        </div>

                        <div class="booking-step" data-step="3" data-view-style="bookings-create-3">
                            <div class="step-header">
                                <h4 class="fw-bold mb-1">{{ __('Confirm Your Booking') }}</h4>
                                <p class="text-secondary small mb-0">{{ __('Review your booking details') }}</p>
                            </div>

                            <div class="booking-summary">
                                <div class="summary-row">
                                    <div class="summary-item">
                                        <span class="summary-label">{{ __('Service') }}</span>
                                        <div class="summary-value" id="summaryService">—</div>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">{{ __('Price') }}</span>
                                        <div class="summary-value text-primary" id="summaryPrice">—</div>
                                    </div>
                                </div>
                                <div class="summary-row">
                                    <div class="summary-item">
                                        <span class="summary-label">{{ __('Date') }}</span>
                                        <div class="summary-value" id="summaryDate">—</div>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">{{ __('Time') }}</span>
                                        <div class="summary-value" id="summaryTime">—</div>
                                    </div>
                                </div>
                                <div class="summary-row">
                                    <div class="summary-item">
                                        <span class="summary-label">{{ __('Duration') }}</span>
                                        <div class="summary-value" id="summaryDuration">—</div>
                                    </div>
                                    <div class="summary-item">
                                        <span class="summary-label">{{ __('Total') }}</span>
                                        <div class="summary-value text-primary fs-5" id="summaryTotal">—</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <label for="notes" class="form-label">
                                    <i class="fas fa-pen me-1"></i>{{ __('Notes (optional)') }}
                                </label>
                                <textarea class="form-control booking-input"
                                        id="notes"
                                        name="notes"
                                        rows="3"
                                        maxlength="1000"
                                    placeholder="{{ __('Any special requests or additional information...') }}">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="booking-actions">
                            <button type="button" class="btn btn-outline-secondary" id="prevStep" data-view-style="bookings-create-4">
                                <i class="fas fa-arrow-left me-2"></i>{{ __('Back') }}
                            </button>
                            <div class="ms-auto d-flex gap-2">
                                <button type="button" class="btn btn-primary" id="nextStep">
                                    {{ __('Next') }}<i class="fas fa-arrow-right ms-2"></i>
                                </button>
                                <button type="submit" class="btn btn-success" id="submitBtn" data-view-style="bookings-create-5">
                                    <i class="fas fa-check me-2"></i>{{ __('Confirm Booking') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <input type="hidden" data-page-script="bookings/create">
@endsection
@push('styles')
    @vite('resources/css/bookings.css')
@endpush
