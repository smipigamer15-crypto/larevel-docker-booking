@extends('layouts.admin')
@section('style-page', 'admin/api-tokens/index')

@section('title', 'API Access')

@section('content')
    <div class="admin-security py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-key text-primary me-2"></i>API Access
                    </h1>
                    <p class="text-secondary mb-0">Manage API tokens & monitor requests</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge-total">
                        <i class="fas fa-key"></i>{{ $stats['total_tokens'] }} Tokens
                    </span>
                    <a href="{{ url('/api/v1/services') }}" target="_blank" class="btn-clear">
                        <i class="fas fa-book"></i>API Docs
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success rounded-3 border-0 mb-3">
                    <i class="fas fa-check-circle me-1"></i>{{ session('success') }}

                    @if(session('new_token'))
                        <div class="mt-2">
                            <strong>Copy this token now — it won't be shown again:</strong>
                            <code class="d-block mt-1 p-2 bg-white rounded border" data-view-style="admin-api-tokens-index-1">
                                {{ session('new_token') }}
                            </code>
                        </div>
                    @endif
                </div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-primary-soft mb-2">
                            <i class="fas fa-key text-primary"></i>
                        </div>
                        <h3 class="fw-bold text-primary mb-0">{{ $stats['total_tokens'] }}</h3>
                        <p class="small text-secondary mb-0">Total Tokens</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-success-soft mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                        <h3 class="fw-bold text-success mb-0">{{ $stats['active_tokens'] }}</h3>
                        <p class="small text-secondary mb-0">Active (7d)</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-purple-soft mb-2">
                            <i class="fas fa-chart-line text-purple"></i>
                        </div>
                        <h3 class="fw-bold text-purple mb-0">{{ $stats['total_requests'] }}</h3>
                        <p class="small text-secondary mb-0">Total Requests</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-warning-soft mb-2">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                        <h3 class="fw-bold text-warning mb-0">{{ $stats['requests_today'] }}</h3>
                        <p class="small text-secondary mb-0">Today</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-plus-circle text-primary me-2"></i>Create New Token
                    </h5>

                    <form action="{{ route('admin.api-tokens.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">User</label>
                                <select name="user_id" class="form-select" required>
                                    <option value="">-- Select User --</option>
                                    @foreach(\App\Models\User::orderBy('name')->get() as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Token Name</label>
                                <input type="text" name="name" class="form-control"
                                    placeholder="e.g. Mobile App, Integration" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Abilities</label>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    @foreach(['bookings:read', 'bookings:write', 'services:read', 'profile:read', 'profile:write'] as $ability)
                                        <label class="ability-check">
                                            <input type="checkbox" name="abilities[]" value="{{ $ability }}"
                                                {{ in_array($ability, ['bookings:read', 'services:read', 'profile:read']) ? 'checked' : '' }}>
                                            <span>{{ $ability }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary-modern">
                                    <i class="fas fa-key me-2"></i>Generate Token
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 ps-4">#</th>
                                    <th class="py-3">Token Name</th>
                                    <th class="py-3">User</th>
                                    <th class="py-3">Abilities</th>
                                    <th class="py-3">Last Used</th>
                                    <th class="py-3 text-center pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tokens as $token)
                                    <tr>
                                        <td class="ps-4 fw-semibold">#{{ $token->id }}</td>

                                        <td>
                                            <i class="fas fa-key text-primary me-1"></i>
                                            <span class="fw-semibold">{{ $token->name }}</span>
                                        </td>

                                        <td>
                                            @if($token->tokenable)
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar bg-primary-soft text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center"
                                                            data-view-style="admin-api-tokens-index-2">
                                                        {{ strtoupper(substr($token->tokenable->name, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold small">{{ $token->tokenable->name }}</div>
                                                        <small class="text-secondary">{{ $token->tokenable->email }}</small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-secondary">Deleted</span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="d-flex gap-1 flex-wrap">
                                                @foreach(($token->abilities ?? []) as $ability)
                                                    <span class="badge bg-light text-dark px-2 py-1 rounded-pill small">
                                                        {{ $ability }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>

                                        <td>
                                            @if($token->last_used_at)
                                                <div class="d-flex flex-column">
                                                    <span class="fw-semibold small">{{ $token->last_used_at->format('M d, Y') }}</span>
                                                    <small class="text-secondary">{{ $token->last_used_at->diffForHumans() }}</small>
                                                </div>
                                            @else
                                                <span class="badge bg-warning-soft text-warning px-3 py-2 rounded-pill">
                                                    Never used
                                                </span>
                                            @endif
                                        </td>

                                        <td class="text-center pe-4">
                                            <form action="{{ route('admin.api-tokens.destroy', $token) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn-action btn-action-delete"
                                                        data-view-action="admin-api-tokens-index-1"
                                                        title="Revoke">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="fas fa-key text-secondary display-4 d-block mb-3"></i>
                                            <h5 class="text-secondary">No API tokens yet</h5>
                                            <p class="text-secondary small">Create your first token above</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-history text-primary me-2"></i>Recent API Requests
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 ps-4">Method</th>
                                    <th class="py-3">Endpoint</th>
                                    <th class="py-3">User</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill">
                                                {{ $log->method }}
                                            </span>
                                        </td>
                                        <td>
                                            <code class="text-dark small">{{ $log->endpoint }}</code>
                                        </td>
                                        <td>
                                            @if($log->user)
                                                <small class="fw-semibold">{{ $log->user->name }}</small>
                                            @else
                                                <span class="text-secondary small">Guest</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $log->status_color }}-soft text-{{ $log->status_color }} px-3 py-2 rounded-pill">
                                                {{ $log->status_code }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-secondary">{{ $log->created_at->diffForHumans() }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-secondary">
                                            No API requests yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($tokens->hasPages())
                <div class="pagination-wrapper">
                    {{ $tokens->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>
    </div>

@endsection
<input type="hidden" data-page-script="admin/api-tokens/index">
