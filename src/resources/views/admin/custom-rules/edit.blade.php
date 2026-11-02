@extends('layouts.admin')
@section('style-page', 'admin/custom-rules/edit')

@section('title', 'Custom Rules — ' . $user->name)

@section('content')
    <div class="admin-security py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-sliders-h text-primary me-2"></i>Custom Rules
                    </h1>
                    <p class="text-secondary mb-0">Configure security for {{ $user->name }}</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge-total">
                        <i class="fas fa-hashtag"></i>ID: {{ $user->id }}
                    </span>
                    <a href="{{ route('admin.custom-rules.index') }}" class="btn-clear">
                        <i class="fas fa-arrow-left"></i>Back
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success rounded-3 border-0 mb-3">
                    <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
                </div>
            @endif

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar bg-primary-soft text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center"
                                data-view-style="admin-custom-rules-edit-1">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                            <p class="text-secondary mb-0">
                                <i class="fas fa-envelope me-1"></i>{{ $user->email }}
                            </p>
                        </div>

                        <form action="{{ route('admin.custom-rules.reset', $user) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-clear"
                                    data-view-action="admin-custom-rules-edit-1" >
                                <i class="fas fa-redo"></i>Reset
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.custom-rules.update', $user) }}" method="POST">
                @csrf @method('PUT')

                <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div>
                                <h5 class="fw-bold mb-1">
                                    <i class="fas fa-power-off text-primary me-2"></i>Enable Custom Rules
                                </h5>
                                <p class="text-secondary small mb-0">
                                    When enabled, all rules below will be applied to this user
                                </p>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_enabled" value="1"
                                    id="is_enabled" data-view-style="admin-custom-rules-edit-2"
                                    {{ old('is_enabled', $settings->is_enabled) ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">

                    <div class="col-lg-6">
                        <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-100">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3">
                                    <i class="fas fa-network-wired text-primary me-2"></i>IP Rules
                                </h5>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">IP Whitelist</label>
                                    <textarea name="ip_whitelist" class="form-control" rows="3"
                                        placeholder="1.2.3.4, 10.0.0.0/24, 192.168.1.*">{{ old('ip_whitelist', implode(', ', $settings->ip_whitelist ?? [])) }}</textarea>
                                    <small class="text-secondary">Only these IPs can login. Empty = all allowed.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">IP Blacklist</label>
                                    <textarea name="ip_blacklist" class="form-control" rows="3"
                                        placeholder="5.6.7.8">{{ old('ip_blacklist', implode(', ', $settings->ip_blacklist ?? [])) }}</textarea>
                                    <small class="text-secondary">These IPs are blocked. Supports CIDR.</small>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label fw-semibold">Blocked Countries</label>
                                    <textarea name="blocked_countries" class="form-control" rows="2"
                                        placeholder="RU, CN, IR">{{ old('blocked_countries', implode(', ', $settings->blocked_countries ?? [])) }}</textarea>
                                    <small class="text-secondary">ISO codes: US, GB, DE, UA...</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card shadow-sm border-0 rounded-4 overflow-hidden h-100">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3">
                                    <i class="fas fa-key text-primary me-2"></i>Login & Session
                                </h5>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Max Login Attempts</label>
                                    <input type="number" name="max_login_attempts" class="form-control"
                                        min="1" max="20"
                                        value="{{ old('max_login_attempts', $settings->max_login_attempts ?? 5) }}">
                                    <small class="text-secondary">After N failed attempts, account locks.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Lockout Duration (min)</label>
                                    <input type="number" name="login_lockout_minutes" class="form-control"
                                        min="1" max="1440"
                                        value="{{ old('login_lockout_minutes', $settings->login_lockout_minutes ?? 15) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Session Timeout (min)</label>
                                    <input type="number" name="session_timeout_minutes" class="form-control"
                                        min="5" max="10080"
                                        value="{{ old('session_timeout_minutes', $settings->session_timeout_minutes ?? 120) }}">
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="require_2fa" value="1"
                                        id="require_2fa"
                                        {{ old('require_2fa', $settings->require_2fa ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="require_2fa">
                                        Require 2FA
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3">
                                    <i class="fas fa-clock text-primary me-2"></i>Time Restrictions
                                </h5>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold d-block">Allowed Hours</label>
                                    <div class="hour-grid">
                                        @for($h = 0; $h < 24; $h++)
                                            <label class="hour-check">
                                                <input type="checkbox" name="allowed_hours[]" value="{{ $h }}"
                                                    {{ in_array($h, old('allowed_hours', $settings->allowed_hours ?? [])) ? 'checked' : '' }}>
                                                <span>{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}</span>
                                            </label>
                                        @endfor
                                    </div>
                                    <small class="text-secondary">Empty = any hour.</small>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label fw-semibold d-block">Allowed Days</label>
                                    <div class="day-grid">
                                        @foreach([1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun'] as $num => $label)
                                            <label class="day-check">
                                                <input type="checkbox" name="allowed_days[]" value="{{ $num }}"
                                                    {{ in_array($num, old('allowed_days', $settings->allowed_days ?? [])) ? 'checked' : '' }}>
                                                <span>{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <small class="text-secondary">Empty = any day.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn-primary-modern">
                                <i class="fas fa-save me-2"></i>Save Rules
                            </button>
                            <a href="{{ route('admin.custom-rules.index') }}" class="btn-clear">
                                <i class="fas fa-times"></i>Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
<input type="hidden" data-page-script="admin/custom-rules/edit">
