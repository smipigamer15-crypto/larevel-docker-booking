@extends('layouts.admin')
@section('style-page', 'admin/security/index')

@section('title', 'Security Inquiries')

@section('content')
    <div class="admin-security py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-shield-alt text-primary me-2"></i>Security Inquiries
                    </h1>
                    <p class="text-secondary mb-0">Manage all security package requests from clients</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge-total">
                        <i class="fas fa-users"></i>{{ $stats['total'] }} Total
                    </span>
                    <a href="{{ route('admin.security.index') }}" class="btn-clear">
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
                            <i class="fas fa-phone text-info"></i>
                        </div>
                        <h3 class="fw-bold text-info mb-0">{{ $stats['contacted'] }}</h3>
                        <p class="small text-secondary mb-0">Contacted</p>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-success-soft mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                        <h3 class="fw-bold text-success mb-0">{{ $stats['approved'] }}</h3>
                        <p class="small text-secondary mb-0">Approved</p>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-danger-soft mb-2">
                            <i class="fas fa-times-circle text-danger"></i>
                        </div>
                        <h3 class="fw-bold text-danger mb-0">{{ $stats['rejected'] }}</h3>
                        <p class="small text-secondary mb-0">Rejected</p>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-purple-soft mb-2">
                            <i class="fas fa-filter text-purple"></i>
                        </div>
                        <a href="{{ route('admin.security.index') }}" class="btn btn-outline-primary w-100 mt-1">
                            <i class="fas fa-arrow-right me-1"></i>All
                        </a>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.security.index') }}" class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search name, email, company..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All statuses</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                <option value="deactivated" {{ request('status') === 'deactivated' ? 'selected' : '' }}>Deactivated</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="package" class="form-select">
                                <option value="">All packages</option>
                                <option value="basic" {{ request('package') === 'basic' ? 'selected' : '' }}>Basic</option>
                                <option value="pro" {{ request('package') === 'pro' ? 'selected' : '' }}>Pro</option>
                                <option value="enterprise" {{ request('package') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                                <option value="custom" {{ request('package') === 'custom' ? 'selected' : '' }}>Custom</option>
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
                                    <th class="py-3">Company</th>
                                    <th class="py-3">Package</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Date</th>
                                    <th class="py-3 text-center pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inquiries as $inquiry)
                                    <tr>
                                        <td class="ps-4 fw-semibold">#{{ $inquiry->id }}</td>

                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar bg-primary-soft text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center"
                                                        data-view-style="admin-security-index-1">
                                                    {{ strtoupper(substr($inquiry->name, 0, 2)) }}
                                                </div>
                                                {{ $inquiry->name }}
                                            </div>
                                        </td>

                                        <td>
                                            <a href="mailto:{{ $inquiry->email }}" class="text-decoration-none text-dark">
                                                {{ $inquiry->email }}
                                            </a>
                                        </td>

                                        <td>{{ $inquiry->company ?? '-' }}</td>

                                        <td>
                                            <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill">
                                                {{ $inquiry->package_label }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="status-badge badge bg-{{ $inquiry->status_color }}-soft text-{{ $inquiry->status_color }} px-3 py-2 rounded-pill">
                                                <i class="fas {{ $inquiry->status == 'pending' ? 'fa-clock' : ($inquiry->status == 'contacted' ? 'fa-phone' : ($inquiry->status == 'approved' ? 'fa-check-circle' : 'fa-times-circle')) }} me-1"></i>
                                                {{ $inquiry->status_label }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold">{{ $inquiry->created_at->format('M d, Y') }}</span>
                                                <small class="text-secondary">{{ $inquiry->created_at->format('H:i') }}</small>
                                            </div>
                                        </td>

                                        <td class="text-center pe-4">
                                            <div class="d-flex gap-1 justify-content-center align-items-center">
                                                <a href="{{ route('admin.security.show', $inquiry->id) }}"
                                                        class="btn-action btn-action-view"
                                                        title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <form action="{{ route('admin.security.destroy', $inquiry->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn-action btn-action-delete"
                                                            data-view-action="admin-security-index-1"
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
                                            <h5 class="text-secondary">No inquiries found</h5>
                                            <p class="text-secondary small">Security requests from clients will appear here</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($inquiries->hasPages())
                <div class="pagination-wrapper">
                    {{ $inquiries->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>
    </div>

@endsection
<input type="hidden" data-page-script="admin/security/index">
