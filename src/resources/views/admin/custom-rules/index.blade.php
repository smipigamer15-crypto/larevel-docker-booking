@extends('layouts.admin')
@section('style-page', 'admin/custom-rules/index')

@section('title', 'Custom Security Rules')

@section('content')
    <div class="admin-security py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-sliders-h text-primary me-2"></i>Custom Security Rules
                    </h1>
                    <p class="text-secondary mb-0">Configure per-user security policies</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge-total">
                        <i class="fas fa-list"></i>{{ $stats['enabled_rules'] }} Active
                    </span>
                    <button class="btn-clear" data-view-action="admin-custom-rules-index-1" >
                        <i class="fas fa-sync-alt"></i>Refresh
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success rounded-3 border-0 mb-3">
                    <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
                </div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-4">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-primary-soft mb-2">
                            <i class="fas fa-users text-primary"></i>
                        </div>
                        <h3 class="fw-bold text-primary mb-0">{{ $stats['total_users'] }}</h3>
                        <p class="small text-secondary mb-0">Total Users</p>
                    </div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-success-soft mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                        <h3 class="fw-bold text-success mb-0">{{ $stats['enabled_rules'] }}</h3>
                        <p class="small text-secondary mb-0">Active Rules</p>
                    </div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-purple-soft mb-2">
                            <i class="fas fa-shield-alt text-purple"></i>
                        </div>
                        <h3 class="fw-bold text-purple mb-0">{{ $stats['total_rules'] }}</h3>
                        <p class="small text-secondary mb-0">Total Configured</p>
                    </div>
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
                                    <th class="py-3">Rules</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3 text-center pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    @php $s = $user->securitySetting; @endphp
                                    <tr>
                                        <td class="ps-4 fw-semibold">#{{ $user->id }}</td>

                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar bg-primary-soft text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center"
                                                        data-view-style="admin-custom-rules-index-1">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $user->name }}</div>
                                                    <small class="text-secondary">{{ $user->email }}</small>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            @if($s)
                                                <div class="d-flex gap-1 flex-wrap">
                                                    @if(!empty($s->ip_whitelist))
                                                        <span class="badge bg-success-soft text-success px-2 py-1 rounded-pill small">
                                                            IP Whitelist ({{ count($s->ip_whitelist) }})
                                                        </span>
                                                    @endif
                                                    @if(!empty($s->ip_blacklist))
                                                        <span class="badge bg-danger-soft text-danger px-2 py-1 rounded-pill small">
                                                            IP Blacklist ({{ count($s->ip_blacklist) }})
                                                        </span>
                                                    @endif
                                                    @if(!empty($s->allowed_hours))
                                                        <span class="badge bg-info-soft text-info px-2 py-1 rounded-pill small">
                                                            Hours
                                                        </span>
                                                    @endif
                                                    @if($s->require_2fa)
                                                        <span class="badge bg-purple-soft text-purple px-2 py-1 rounded-pill small">
                                                            2FA
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-secondary small">No custom rules</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if($s && $s->is_enabled)
                                                <span class="badge bg-success-soft text-success px-3 py-2 rounded-pill">
                                                    <i class="fas fa-check-circle me-1"></i>Enabled
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-soft text-secondary px-3 py-2 rounded-pill">
                                                    <i class="fas fa-pause-circle me-1"></i>Disabled
                                                </span>
                                            @endif
                                        </td>

                                        <td class="text-center pe-4">
                                            <a href="{{ route('admin.custom-rules.edit', $user) }}"
                                                    class="btn-action btn-action-view" title="Configure">
                                                <i class="fas fa-cog"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="fas fa-users text-secondary display-4 d-block mb-3"></i>
                                            <h5 class="text-secondary">No users yet</h5>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($users->hasPages())
                <div class="pagination-wrapper">
                    {{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>
    </div>

@endsection
<input type="hidden" data-page-script="admin/custom-rules/index">
