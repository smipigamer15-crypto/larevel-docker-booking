@extends('layouts.admin')
@section('style-page', 'admin/firewall/index')

@section('title', 'Firewall')

@section('content')
    <div class="admin-security py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-shield-alt text-primary me-2"></i>Advanced Firewall
                    </h1>
                    <p class="text-secondary mb-0">Manage IP rules, rate limits & security policies</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge-total">
                        <i class="fas fa-shield-alt"></i>{{ $stats['total'] }} Rules
                    </span>
                    <a href="{{ route('admin.firewall.logs') }}" class="btn-clear">
                        <i class="fas fa-list"></i>View Logs
                    </a>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-primary-soft mb-2">
                            <i class="fas fa-list text-primary"></i>
                        </div>
                        <h3 class="fw-bold text-primary mb-0">{{ $stats['total'] }}</h3>
                        <p class="small text-secondary mb-0">Total Rules</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-success-soft mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                        <h3 class="fw-bold text-success mb-0">{{ $stats['whitelist'] }}</h3>
                        <p class="small text-secondary mb-0">Whitelisted</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-danger-soft mb-2">
                            <i class="fas fa-ban text-danger"></i>
                        </div>
                        <h3 class="fw-bold text-danger mb-0">{{ $stats['blacklist'] }}</h3>
                        <p class="small text-secondary mb-0">Blacklisted</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-purple-soft mb-2">
                            <i class="fas fa-shield-virus text-purple"></i>
                        </div>
                        <h3 class="fw-bold text-purple mb-0">{{ $stats['blocked_today'] }}</h3>
                        <p class="small text-secondary mb-0">Blocked Today</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-plus-circle text-primary me-2"></i>Add New Rule
                    </h5>

                    @if(session('success'))
                        <div class="alert alert-success rounded-3 border-0 mb-3">
                            <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger rounded-3 border-0 mb-3">
                            <i class="fas fa-exclamation-circle me-1"></i>{{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.firewall.rules.store') }}">
                        @csrf
                        <div class="row g-3">

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Type</label>
                                <select name="type" class="form-select" required>
                                    <option value="blacklist" {{ old('type') == 'blacklist' ? 'selected' : '' }}>
                                        Blacklist (Block)
                                    </option>
                                    <option value="whitelist" {{ old('type') == 'whitelist' ? 'selected' : '' }}>
                                        Whitelist (Allow)
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">IP / Range</label>
                                <input type="text"
                                    name="value"
                                    class="form-control @error('value') is-invalid @enderror"
                                    value="{{ old('value') }}"
                                    placeholder="192.168.1.1 or 10.0.0.0/24"
                                    required>
                                <small class="text-secondary">CIDR: 10.0.0.0/24 · Wildcard: 192.168.1.*</small>
                                @error('value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Reason</label>
                                <input type="text"
                                    name="reason"
                                    class="form-control"
                                    value="{{ old('reason') }}"
                                    placeholder="Spam, attack, etc.">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Expires At</label>
                                <input type="datetime-local"
                                    name="expires_at"
                                    class="form-control"
                                    value="{{ old('expires_at') }}">
                                <small class="text-secondary">Empty = forever</small>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn-primary-modern">
                                    <i class="fas fa-plus me-2"></i>Add Rule
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('admin.firewall.index') }}" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small mb-1">Filter by Type</label>
                            <select name="type" class="form-select form-select-sm">
                                <option value="">All Types</option>
                                <option value="whitelist" {{ request('type') == 'whitelist' ? 'selected' : '' }}>Whitelist</option>
                                <option value="blacklist" {{ request('type') == 'blacklist' ? 'selected' : '' }}>Blacklist</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small mb-1">Filter by Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn-primary-modern w-100">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('admin.firewall.index') }}" class="btn-clear">
                                <i class="fas fa-times"></i>Reset
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
                                    <th class="py-3">Type</th>
                                    <th class="py-3">IP / Range</th>
                                    <th class="py-3">Reason</th>
                                    <th class="py-3">Expires</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3 text-center pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rules as $rule)
                                    <tr>
                                        <td class="ps-4 fw-semibold">#{{ $rule->id }}</td>

                                        <td>
                                            <span class="status-badge badge bg-{{ $rule->isWhitelist() ? 'success' : 'danger' }}-soft text-{{ $rule->isWhitelist() ? 'success' : 'danger' }} px-3 py-2 rounded-pill">
                                                <i class="fas {{ $rule->isWhitelist() ? 'fa-check-circle' : 'fa-ban' }} me-1"></i>
                                                {{ ucfirst($rule->type) }}
                                            </span>
                                        </td>

                                        <td>
                                            <code class="text-dark fw-semibold">{{ $rule->value }}</code>
                                        </td>

                                        <td class="text-secondary small">{{ $rule->reason ?? '—' }}</td>

                                        <td>
                                            @if($rule->expires_at)
                                                <span class="small {{ $rule->isExpired() ? 'text-danger fw-semibold' : 'text-secondary' }}">
                                                    <i class="fas fa-clock me-1"></i>
                                                    {{ $rule->expires_at->format('M d, Y H:i') }}
                                                </span>
                                            @else
                                                <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                                                    <i class="fas fa-infinity me-1"></i>Never
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="badge bg-{{ $rule->is_active ? 'success' : 'secondary' }}-soft text-{{ $rule->is_active ? 'success' : 'secondary' }} px-3 py-2 rounded-pill">
                                                <i class="fas {{ $rule->is_active ? 'fa-check-circle' : 'fa-pause-circle' }} me-1"></i>
                                                {{ $rule->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>

                                        <td class="text-center pe-4">
                                            <div class="d-flex gap-1 justify-content-center align-items-center">

                                                <form action="{{ route('admin.firewall.rules.update', $rule) }}"
                                                        method="POST"
                                                        class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="is_active" value="{{ $rule->is_active ? 0 : 1 }}">
                                                    <button type="submit"
                                                            class="btn-action {{ $rule->is_active ? 'btn-action-pause' : 'btn-action-play' }}"
                                                            title="{{ $rule->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i class="fas {{ $rule->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.firewall.rules.destroy', $rule) }}"
                                                        method="POST"
                                                        class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn-action btn-action-delete"
                                                            data-view-action="admin-firewall-index-1"
                                                            title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="fas fa-shield-alt text-secondary display-4 d-block mb-3"></i>
                                            <h5 class="text-secondary">No firewall rules yet</h5>
                                            <p class="text-secondary small">Add your first rule above to protect your application</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($rules->hasPages())
                <div class="pagination-wrapper">
                    {{ $rules->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>
    </div>

@endsection
<input type="hidden" data-page-script="admin/firewall/index">
