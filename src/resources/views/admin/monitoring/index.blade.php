@extends('layouts.admin')
@section('style-page', 'admin/monitoring/index')

@section('title', '24/7 Monitoring')

@section('content')
    <div class="admin-security py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
                <div>
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-eye text-primary me-2"></i>24/7 Monitoring
                    </h1>
                    <p class="text-secondary mb-0">Real-time activity & security monitoring</p>
                </div>
                <div class="d-flex gap-2 align-items-center flex-wrap justify-content-end">
                    <span class="badge-total mb-1">
                        <i class="fas fa-database"></i>{{ $stats['total'] }} Total
                    </span>

                    <form method="POST" action="{{ route('admin.monitoring.clear') }}" class="d-inline m-0 mb-1">
                        @csrf
                        <input type="hidden" name="days" value="30">
                        <button type="submit" class="btn-clear" data-view-action="admin-monitoring-index-1" >
                            <i class="fas fa-trash"></i>Clear Old
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.monitoring.clear-all') }}" class="d-inline m-0 mb-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-clear text-danger" data-view-style="admin-monitoring-index-1" data-view-action="admin-monitoring-index-2" >
                            <i class="fas fa-bomb"></i>Clear All
                        </button>
                    </form>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-2">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-primary-soft mb-2">
                            <i class="fas fa-database text-primary"></i>
                        </div>
                        <h3 class="fw-bold text-primary mb-0">{{ $stats['total'] }}</h3>
                        <p class="small text-secondary mb-0">Total Logs</p>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-info-soft mb-2">
                            <i class="fas fa-clock text-info"></i>
                        </div>
                        <h3 class="fw-bold text-info mb-0">{{ $stats['last_24h'] }}</h3>
                        <p class="small text-secondary mb-0">Last 24h</p>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-warning-soft mb-2">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                        </div>
                        <h3 class="fw-bold text-warning mb-0">{{ $stats['warnings_24h'] }}</h3>
                        <p class="small text-secondary mb-0">Warnings</p>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-danger-soft mb-2">
                            <i class="fas fa-times-circle text-danger"></i>
                        </div>
                        <h3 class="fw-bold text-danger mb-0">{{ $stats['errors_24h'] }}</h3>
                        <p class="small text-secondary mb-0">Errors</p>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-danger-soft mb-2">
                            <i class="fas fa-skull-crossbones text-danger"></i>
                        </div>
                        <h3 class="fw-bold text-danger mb-0">{{ $stats['critical_24h'] }}</h3>
                        <p class="small text-secondary mb-0">Critical</p>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-success-soft mb-2">
                            <i class="fas fa-shield-alt text-success"></i>
                        </div>
                        <h3 class="fw-bold text-success mb-0">On</h3>
                        <p class="small text-secondary mb-0">Status</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.monitoring.index') }}" class="row g-3 align-items-center">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search event or description..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="level" class="form-select">
                                <option value="">All levels</option>
                                <option value="info" {{ request('level') === 'info' ? 'selected' : '' }}>Info</option>
                                <option value="warning" {{ request('level') === 'warning' ? 'selected' : '' }}>Warning</option>
                                <option value="error" {{ request('level') === 'error' ? 'selected' : '' }}>Error</option>
                                <option value="critical" {{ request('level') === 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="user_id" class="form-select">
                                <option value="">All users</option>
                                @foreach($users ?? [] as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="subscription" class="form-select">
                                <option value="">All subscriptions</option>
                                <option value="active" {{ request('subscription') === 'active' ? 'selected' : '' }}>Active subscribers</option>
                                <option value="basic" {{ request('subscription') === 'basic' ? 'selected' : '' }}>Basic plan</option>
                                <option value="pro" {{ request('subscription') === 'pro' ? 'selected' : '' }}>Pro plan</option>
                                <option value="enterprise" {{ request('subscription') === 'enterprise' ? 'selected' : '' }}>Enterprise plan</option>
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
                                    <th class="py-3">Level</th>
                                    <th class="py-3">Event</th>
                                    <th class="py-3">Description</th>
                                    <th class="py-3">User</th>
                                    <th class="py-3">IP</th>
                                    <th class="py-3">Date</th>
                                    <th class="py-3 text-center pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="ps-4 fw-semibold">#{{ $log->id }}</td>

                                        <td>
                                            <span class="status-badge badge bg-{{ $log->level_color }}-soft text-{{ $log->level_color }} px-3 py-2 rounded-pill">
                                                <i class="fas {{ $log->level_icon }} me-1"></i>
                                                {{ ucfirst($log->level) }}
                                            </span>
                                        </td>

                                        <td>
                                            <code class="text-primary" data-view-style="admin-monitoring-index-2">
                                                {{ $log->event }}
                                            </code>
                                        </td>

                                        <td>
                                            <div class="text-truncate" data-view-style="admin-monitoring-index-3" title="{{ $log->description }}">
                                                {{ $log->description }}
                                            </div>
                                        </td>

                                        <td>
                                            @if($log->user)
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar bg-primary-soft text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center"
                                                            data-view-style="admin-monitoring-index-4">
                                                        {{ strtoupper(substr($log->user->name, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold small">{{ $log->user->name }}</div>
                                                        <small class="text-secondary">{{ $log->user->email }}</small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="badge bg-secondary-soft text-secondary px-3 py-2 rounded-pill">
                                                    Guest
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            <code data-view-style="admin-monitoring-index-5">
                                                {{ $log->ip_address ?? '—' }}
                                            </code>
                                        </td>

                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold small">{{ $log->created_at->format('M d, Y') }}</span>
                                                <small class="text-secondary">
                                                    {{ $log->created_at->format('H:i') }} · {{ $log->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </td>

                                        <td class="text-center pe-4">
                                            <a href="{{ route('admin.monitoring.show', $log->id) }}"
                                                    class="btn-action btn-action-view"
                                                    title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="fas fa-inbox text-secondary display-4 d-block mb-3"></i>
                                            <h5 class="text-secondary">No logs found</h5>
                                            <p class="text-secondary small">Activity logs will appear here</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($logs->hasPages())
                <div class="pagination-wrapper">
                    {{ $logs->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>
    </div>

@endsection
<input type="hidden" data-page-script="admin/monitoring/index">
