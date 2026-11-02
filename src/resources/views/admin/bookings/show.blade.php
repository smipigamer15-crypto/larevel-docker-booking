@extends('layouts.admin')
@section('style-page', 'admin/bookings/show')

@section('title', 'Booking #' . $booking->id)

@section('content')
    <div class="admin-security py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-calendar-check text-primary me-2"></i>Booking #{{ $booking->id }}
                    </h1>
                    <p class="text-secondary mb-0">View booking details</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge-total">
                        <i class="fas fa-hashtag"></i>ID: {{ $booking->id }}
                    </span>
                    <a href="{{ route('admin.bookings.index') }}" class="btn-clear">
                        <i class="fas fa-arrow-left"></i>Back
                    </a>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 flex-wrap">

                        <div class="avatar-lg bg-primary-soft text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center"
                                data-view-style="admin-bookings-show-1">
                            <i class="fas fa-calendar-check"></i>
                        </div>

                        <div class="flex-grow-1">
                            <h4 class="fw-bold mb-1">{{ $booking->service->title ?? 'N/A' }}</h4>
                            <p class="text-secondary mb-2">
                                <i class="fas fa-user me-1"></i>{{ $booking->user->name ?? 'N/A' }}
                                <span class="mx-2">·</span>
                                <i class="fas fa-calendar-day me-1"></i>{{ $booking->booking_date }}
                                <span class="mx-2">·</span>
                                <i class="fas fa-clock me-1"></i>{{ $booking->start_time }} - {{ $booking->end_time }}
                            </p>
                            <span class="status-badge badge bg-{{ $booking->status == 'upcoming' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'secondary') }}-soft text-{{ $booking->status == 'upcoming' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'secondary') }} px-3 py-2 rounded-pill">
                                <i class="fas {{ $booking->status == 'upcoming' ? 'fa-clock' : ($booking->status == 'cancelled' ? 'fa-times-circle' : 'fa-check-circle') }} me-1"></i>
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>

                        <div class="text-end d-none d-md-block">
                            <div class="small text-secondary text-uppercase fw-semibold" data-view-style="admin-bookings-show-2">Created</div>
                            <div class="fw-semibold">{{ $booking->created_at->format('M d, Y') }}</div>
                            <small class="text-secondary">{{ $booking->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-100">
                        <div class="card-header-custom">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-user text-primary me-2"></i>Customer Information
                            </h5>
                        </div>
                        <div class="card-body-custom">

                            <div class="info-row">
                                <span class="info-label">
                                    <i class="fas fa-user me-1"></i>Name
                                </span>
                                <span class="info-value">{{ $booking->user->name ?? 'N/A' }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">
                                    <i class="fas fa-envelope me-1"></i>Email
                                </span>
                                <span class="info-value">
                                    @if($booking->user->email ?? false)
                                        <a href="mailto:{{ $booking->user->email }}" class="text-decoration-none text-dark">
                                            {{ $booking->user->email }}
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">
                                    <i class="fas fa-phone me-1"></i>Phone
                                </span>
                                <span class="info-value">{{ $booking->user->phone ?? 'Not provided' }}</span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">
                                    <i class="fas fa-calendar-alt me-1"></i>Member Since
                                </span>
                                <span class="info-value">
                                    {{ $booking->user->created_at ? $booking->user->created_at->format('M d, Y') : 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-100">
                        <div class="card-header-custom">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-calendar-check text-primary me-2"></i>Booking Details
                            </h5>
                        </div>
                        <div class="card-body-custom">

                            <div class="info-row">
                                <span class="info-label">
                                    <i class="fas fa-concierge-bell me-1"></i>Service
                                </span>
                                <span class="info-value fw-bold text-primary">
                                    {{ $booking->service->title ?? 'N/A' }}
                                </span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">
                                    <i class="fas fa-calendar-day me-1"></i>Date
                                </span>
                                <span class="info-value">
                                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}
                                </span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">
                                    <i class="fas fa-clock me-1"></i>Time
                                </span>
                                <span class="info-value">
                                    {{ $booking->start_time }} - {{ $booking->end_time }}
                                </span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">
                                    <i class="fas fa-info-circle me-1"></i>Status
                                </span>
                                <span class="info-value">
                                    <span class="status-badge badge bg-{{ $booking->status == 'upcoming' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'secondary') }}-soft text-{{ $booking->status == 'upcoming' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'secondary') }} px-3 py-2 rounded-pill">
                                        <i class="fas {{ $booking->status == 'upcoming' ? 'fa-clock' : ($booking->status == 'cancelled' ? 'fa-times-circle' : 'fa-check-circle') }} me-1"></i>
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </span>
                            </div>

                            @if($booking->notes)
                                <div class="info-row">
                                    <span class="info-label">
                                        <i class="fas fa-sticky-note me-1"></i>Notes
                                    </span>
                                    <span class="info-value">{{ $booking->notes }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                        <div class="card-header-custom">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-edit text-primary me-2"></i>Update Status
                            </h5>
                        </div>
                        <div class="card-body-custom">
                            <form method="POST" action="{{ route('admin.bookings.update', $booking->id) }}">
                                @csrf
                                @method('PUT')
                                <div class="row align-items-end g-3">

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Change Status</label>
                                        <select class="form-select" name="status">
                                            <option value="upcoming" {{ $booking->status == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                            <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <button type="submit" class="btn-primary-modern w-100">
                                            <i class="fas fa-save me-2"></i>Update Status
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="{{ route('admin.bookings.index') }}" class="btn-clear">
                            <i class="fas fa-arrow-left"></i>Back to List
                        </a>
                        <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" class="d-inline m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-danger-modern" data-view-action="admin-bookings-show-1" >
                                <i class="fas fa-trash-alt"></i>Delete Booking
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
<input type="hidden" data-page-script="admin/bookings/show">
