@extends('layouts.admin')
@section('style-page', 'admin/subscriptions/index')

@section('title', 'Booking Subscriptions')

@section('content')
    <div class="admin-security py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-crown text-primary me-2"></i>Booking Subscriptions
                    </h1>
                    <p class="text-secondary mb-0">Manage all booking plans and clients</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge-total">
                        <i class="fas fa-users"></i>{{ $stats['total'] }} Total
                    </span>
                    <a href="{{ route('admin.subscriptions.index') }}" class="btn-clear">
                        <i class="fas fa-sync-alt"></i>Refresh
                    </a>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-2">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-primary-soft mb-2">
                            <i class="fas fa-database text-primary"></i>
                        </div>
                        <h3 class="fw-bold text-primary mb-0">{{ $stats['total'] }}</h3>
                        <p class="small text-secondary mb-0">Total</p>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-success-soft mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                        <h3 class="fw-bold text-success mb-0">{{ $stats['active'] }}</h3>
                        <p class="small text-secondary mb-0">Active</p>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-warning-soft mb-2">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                        <h3 class="fw-bold text-warning mb-0">{{ $stats['pending'] }}</h3>
                        <p class="small text-secondary mb-0">Pending</p>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-info-soft mb-2">
                            <i class="fas fa-hourglass-half text-info"></i>
                        </div>
                        <h3 class="fw-bold text-info mb-0">{{ $stats['cancelling'] }}</h3>
                        <p class="small text-secondary mb-0">Cancelling</p>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-purple-soft mb-2">
                            <i class="fas fa-crown text-purple"></i>
                        </div>
                        <h3 class="fw-bold text-purple mb-0">{{ $stats['pro'] }}</h3>
                        <p class="small text-secondary mb-0">Pro</p>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-danger-soft mb-2">
                            <i class="fas fa-dollar-sign text-danger"></i>
                        </div>
                        <h3 class="fw-bold text-danger mb-0">${{ $stats['mrr'] }}</h3>
                        <p class="small text-secondary mb-0">MRR</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search name, email, company..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All statuses</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="deactivated" {{ request('status') === 'deactivated' ? 'selected' : '' }}>Deactivated</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="package" class="form-select">
                                <option value="">All plans</option>
                                <option value="pro" {{ request('package') === 'pro' ? 'selected' : '' }}>Pro</option>
                                <option value="enterprise" {{ request('package') === 'enterprise' ? 'selected' : '' }}>Business</option>
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
                                    <th class="py-3">Name</th>
                                    <th class="py-3">Email</th>
                                    <th class="py-3">Plan</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Next billing</th>
                                    <th class="py-3">Days left</th>
                                    <th class="py-3 text-center pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subscriptions as $sub)
                                    <tr>
                                        <td class="ps-4 fw-semibold">#{{ $sub->id }}</td>

                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar bg-primary-soft text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center"
                                                        data-view-style="admin-subscriptions-index-1">
                                                    {{ strtoupper(substr($sub->name, 0, 2)) }}
                                                </div>
                                                {{ $sub->name }}
                                            </div>
                                        </td>

                                        <td>
                                            <a href="mailto:{{ $sub->email }}" class="text-decoration-none text-dark">
                                                {{ $sub->email }}
                                            </a>
                                        </td>

                                        <td>
                                            <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill">
                                                {{ $sub->plan_label }}
                                            </span>
                                        </td>

                                        <td>
                                            @if($sub->is_active && !$sub->cancel_at_period_end)
                                                <span class="status-badge badge bg-success-soft text-success px-3 py-2 rounded-pill">
                                                    <i class="fas fa-check-circle me-1"></i>Active
                                                </span>
                                            @elseif($sub->cancel_at_period_end)
                                                <span class="status-badge badge bg-warning-soft text-warning px-3 py-2 rounded-pill">
                                                    <i class="fas fa-clock me-1"></i>Cancelling
                                                </span>
                                            @elseif($sub->status === 'pending')
                                                <span class="status-badge badge bg-secondary-soft text-secondary px-3 py-2 rounded-pill">
                                                    <i class="fas fa-hourglass-half me-1"></i>Pending
                                                </span>
                                            @else
                                                <span class="status-badge badge bg-danger-soft text-danger px-3 py-2 rounded-pill">
                                                    <i class="fas fa-times-circle me-1"></i>{{ ucfirst($sub->status) }}
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold">{{ $sub->subscription_ends_at?->format('M d, Y') ?? '—' }}</span>
                                            </div>
                                        </td>

                                        <td>
                                            @if($sub->subscription_ends_at && $sub->is_active)
                                                <span class="fw-bold {{ $sub->days_remaining <= 3 ? 'text-danger' : ($sub->days_remaining <= 7 ? 'text-warning' : '') }}">
                                                    {{ $sub->days_remaining }}
                                                </span>
                                            @else
                                                <span class="text-secondary">—</span>
                                            @endif
                                        </td>

                                        <td class="text-center pe-4">
                                            <div class="d-flex gap-1 justify-content-center align-items-center">
                                                <a href="{{ route('admin.subscriptions.show', $sub->id) }}"
                                                        class="btn-action btn-action-view"
                                                        title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <form action="{{ route('admin.subscriptions.destroy', $sub->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn-action btn-action-delete"
                                                            data-view-action="admin-subscriptions-index-1"
                                                            title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-inbox text-secondary display-4 d-block mb-3"></i>
                                            <h5 class="text-secondary">No subscriptions found</h5>
                                            <p class="text-secondary small">Booking subscriptions will appear here</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($subscriptions->hasPages())
                <div class="pagination-wrapper">
                    {{ $subscriptions->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>
    </div>

@endsection
<input type="hidden" data-page-script="admin/subscriptions/index">
