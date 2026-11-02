@extends('layouts.admin')
@section('style-page', 'admin/bookings/index')

@section('title', 'Manage Bookings')

@section('content')
    <div class="admin-security py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-calendar-check text-primary me-2"></i>Bookings
                    </h1>
                    <p class="text-secondary mb-0">Manage all bookings</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge-total">
                        <i class="fas fa-calendar-check"></i>{{ $bookings->total() }} Total
                    </span>
                    <button class="btn-clear" data-view-action="admin-bookings-index-1" >
                        <i class="fas fa-sync-alt"></i>Refresh
                    </button>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-primary-soft mb-2">
                            <i class="fas fa-calendar-check text-primary"></i>
                        </div>
                        <h3 class="fw-bold text-primary mb-0">{{ $bookings->total() }}</h3>
                        <p class="small text-secondary mb-0">Total Bookings</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-success-soft mb-2">
                            <i class="fas fa-clock text-success"></i>
                        </div>
                        <h3 class="fw-bold text-success mb-0">
                            {{ \App\Models\Booking::where('status', 'upcoming')->count() }}
                        </h3>
                        <p class="small text-secondary mb-0">Upcoming</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-purple-soft mb-2">
                            <i class="fas fa-check-circle text-purple"></i>
                        </div>
                        <h3 class="fw-bold text-purple mb-0">
                            {{ \App\Models\Booking::where('status', 'completed')->count() }}
                        </h3>
                        <p class="small text-secondary mb-0">Completed</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-danger-soft mb-2">
                            <i class="fas fa-times-circle text-danger"></i>
                        </div>
                        <h3 class="fw-bold text-danger mb-0">
                            {{ \App\Models\Booking::where('status', 'cancelled')->count() }}
                        </h3>
                        <p class="small text-secondary mb-0">Cancelled</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.bookings.index') }}" class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search name, email..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All statuses</option>
                                <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="service_id" class="form-select">
                                <option value="">All services</option>
                                @foreach($services ?? [] as $service)
                                    <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 ps-4">#</th>
                                    <th class="py-3">User</th>
                                    <th class="py-3">Service</th>
                                    <th class="py-3">Date</th>
                                    <th class="py-3">Time</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3 text-center pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                    <tr>
                                        <td class="ps-4 fw-semibold">#{{ $booking->id }}</td>

                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar bg-primary-soft text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center"
                                                        data-view-style="admin-bookings-index-1">
                                                    {{ strtoupper(substr($booking->user->name ?? 'NA', 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $booking->user->name ?? 'N/A' }}</div>
                                                    @if($booking->user->email ?? false)
                                                        <small class="text-secondary">{{ $booking->user->email }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar bg-purple-soft text-purple rounded-circle d-flex align-items-center justify-content-center"
                                                        data-view-style="admin-bookings-index-2">
                                                    <i class="fas fa-concierge-bell"></i>
                                                </div>
                                                <div class="fw-semibold">{{ $booking->service->title ?? 'N/A' }}</div>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold small">
                                                    <i class="fas fa-calendar-day text-secondary me-1"></i>
                                                    {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}
                                                </span>
                                                <small class="text-secondary">
                                                    {{ \Carbon\Carbon::parse($booking->booking_date)->diffForHumans() }}
                                                </small>
                                            </div>
                                        </td>

                                        <td>
                                            <span class="text-secondary small">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $booking->start_time }} - {{ $booking->end_time }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="status-badge badge bg-{{ $booking->status == 'upcoming' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'secondary') }}-soft text-{{ $booking->status == 'upcoming' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : 'secondary') }} px-3 py-2 rounded-pill">
                                                <i class="fas {{ $booking->status == 'upcoming' ? 'fa-clock' : ($booking->status == 'cancelled' ? 'fa-times-circle' : 'fa-check-circle') }} me-1"></i>
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>

                                        <td class="text-center pe-4">
                                            <div class="d-flex gap-1 justify-content-center align-items-center">
                                                <a href="{{ route('admin.bookings.show', $booking->id) }}"
                                                        class="btn-action btn-action-view"
                                                        title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="fas fa-calendar-check text-secondary display-4 d-block mb-3"></i>
                                            <h5 class="text-secondary">No bookings found</h5>
                                            <p class="text-secondary small">Try adjusting your filters</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($bookings->hasPages())
                <div class="pagination-wrapper">
                    {{ $bookings->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>
    </div>

@endsection
<input type="hidden" data-page-script="admin/bookings/index">
