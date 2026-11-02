@extends('layouts.admin')
@section('style-page', 'admin/firewall/logs')

@section('title', 'Firewall Logs')

@section('content')
    <div class="admin-security py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-shield-virus text-primary me-2"></i>Firewall Logs
                    </h1>
                    <p class="text-secondary mb-0">Blocked requests & security events</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge-total">
                        <i class="fas fa-list"></i>{{ $stats['total'] }} Events
                    </span>
                    <a href="{{ route('admin.firewall.index') }}" class="btn-clear">
                        <i class="fas fa-arrow-left"></i>Back to Rules
                    </a>
                    <form action="{{ route('admin.firewall.clear-logs') }}" method="POST" class="d-inline m-0">
                        @csrf
                        <button type="submit" class="btn-clear" data-view-action="admin-firewall-logs-1" >
                            <i class="fas fa-trash-alt"></i>Clear Old
                        </button>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success rounded-3 border-0 mb-3">
                    <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
                </div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-primary-soft mb-2">
                            <i class="fas fa-list text-primary"></i>
                        </div>
                        <h3 class="fw-bold text-primary mb-0">{{ $stats['total'] }}</h3>
                        <p class="small text-secondary mb-0">Total Events</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-danger-soft mb-2">
                            <i class="fas fa-shield-virus text-danger"></i>
                        </div>
                        <h3 class="fw-bold text-danger mb-0">{{ $stats['today'] }}</h3>
                        <p class="small text-secondary mb-0">Today</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-warning-soft mb-2">
                            <i class="fas fa-calendar-week text-warning"></i>
                        </div>
                        <h3 class="fw-bold text-warning mb-0">{{ $stats['week'] }}</h3>
                        <p class="small text-secondary mb-0">Last 7 Days</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-purple-soft mb-2">
                            <i class="fas fa-network-wired text-purple"></i>
                        </div>
                        <h3 class="fw-bold text-purple mb-0">{{ $stats['unique_ips'] }}</h3>
                        <p class="small text-secondary mb-0">Unique IPs</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('admin.firewall.logs') }}" class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small mb-1">Search IP</label>
                            <input type="text"
                                name="ip"
                                class="form-control form-control-sm"
                                value="{{ request('ip') }}"
                                placeholder="192.168.1.1">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small mb-1">Reason</label>
                            <select name="reason" class="form-select form-select-sm">
                                <option value="">All Reasons</option>
                                <option value="blacklist" {{ request('reason') == 'blacklist' ? 'selected' : '' }}>Blacklist</option>
                                <option value="rate_limit" {{ request('reason') == 'rate_limit' ? 'selected' : '' }}>Rate Limit</option>
                                <option value="attack_pattern" {{ request('reason') == 'attack_pattern' ? 'selected' : '' }}>Attack Pattern</option>
                                <option value="country_block" {{ request('reason') == 'country_block' ? 'selected' : '' }}>Country Block</option>
                                <option value="auto_ban" {{ request('reason') == 'auto_ban' ? 'selected' : '' }}>Auto Ban</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn-primary-modern w-100">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('admin.firewall.logs') }}" class="btn-clear">
                                <i class="fas fa-times"></i>
                            </a>
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
                                    <th class="py-3">IP</th>
                                    <th class="py-3">Reason</th>
                                    <th class="py-3">Request</th>
                                    <th class="py-3">User</th>
                                    <th class="py-3">Blocked At</th>
                                    <th class="py-3 text-center pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="ps-4 fw-semibold">#{{ $log->id }}</td>

                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar bg-danger-soft text-danger fw-bold rounded-circle d-flex align-items-center justify-content-center"
                                                        data-view-style="admin-firewall-logs-1">
                                                    <i class="fas fa-network-wired"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">
                                                        <code class="text-dark">{{ $log->ip }}</code>
                                                    </div>
                                                    @if($log->country)
                                                        <small class="text-secondary">
                                                            <i class="fas fa-globe me-1"></i>{{ $log->country }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <span class="status-badge badge bg-{{ $log->reason_color }}-soft text-{{ $log->reason_color }} px-3 py-2 rounded-pill">
                                                <i class="fas fa-shield-alt me-1"></i>
                                                {{ $log->reason_label }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="badge bg-light text-dark rounded-pill small align-self-start mb-1">
                                                    {{ $log->method }}
                                                </span>
                                                <small class="text-secondary" data-view-style="admin-firewall-logs-2">
                                                    {{ $log->url }}
                                                </small>
                                            </div>
                                        </td>

                                        <td>
                                            @if($log->user)
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar bg-primary-soft text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center"
                                                            data-view-style="admin-firewall-logs-3">
                                                        {{ strtoupper(substr($log->user->name, 0, 2)) }}
                                                    </div>
                                                    <small class="fw-semibold">{{ $log->user->name }}</small>
                                                </div>
                                            @else
                                                <span class="badge bg-light text-secondary px-3 py-2 rounded-pill">
                                                    <i class="fas fa-user-secret me-1"></i>Guest
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold small">{{ $log->blocked_at->format('M d, Y') }}</span>
                                                <small class="text-secondary">
                                                    {{ $log->blocked_at->format('H:i') }}
                                                    · {{ $log->blocked_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </td>

                                        <td class="text-center pe-4">
                                            <div class="d-flex gap-1 justify-content-center align-items-center">
                                                <form action="{{ route('admin.firewall.block-ip') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="ip" value="{{ $log->ip }}">
                                                    <input type="hidden" name="reason" value="Blocked from logs">
                                                    <button type="submit"
                                                            class="btn-action btn-action-delete"
                                                            title="Block this IP permanently"
                                                            data-view-action="admin-firewall-logs-2" data-argument-0="{{ $log->ip }}">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="fas fa-shield-virus text-secondary display-4 d-block mb-3"></i>
                                            <h5 class="text-secondary">No firewall events</h5>
                                            <p class="text-secondary small">Blocked requests will appear here</p>
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

            <div class="results-wrapper">
                <span class="results-info">
                    Showing <strong>{{ $logs->firstItem() ?? 0 }}</strong>
                    to <strong>{{ $logs->lastItem() ?? 0 }}</strong>
                    of <strong>{{ $logs->total() }}</strong> results
                </span>
            </div>

        </div>
    </div>

@endsection
<input type="hidden" data-page-script="admin/firewall/logs">
