@extends('layouts.admin')
@section('style-page', 'admin/monitoring/show')

@section('title', 'Log #' . $log->id)

@section('content')
    <div class="admin-security py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-file-alt text-primary me-2"></i>Log Details #{{ $log->id }}
                    </h1>
                    <p class="text-secondary mb-0">{{ $log->created_at->format('F d, Y \a\t H:i:s') }}</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge-total">
                        <i class="fas fa-hashtag"></i>ID: {{ $log->id }}
                    </span>
                    <a href="{{ route('admin.monitoring.index') }}" class="btn-clear">
                        <i class="fas fa-arrow-left"></i>Back to Logs
                    </a>
                </div>
            </div>

            <div class="row g-4">

                <div class="col-lg-8">

                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                        <div class="card-header-custom">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-info-circle text-primary me-2"></i>Event Information
                            </h5>
                            <span class="status-badge badge bg-{{ $log->level_color }}-soft text-{{ $log->level_color }} px-3 py-2 rounded-pill">
                                <i class="fas {{ $log->level_icon }} me-1"></i>
                                {{ ucfirst($log->level) }}
                            </span>
                        </div>
                        <div class="card-body-custom">

                            <div class="info-row">
                                <span class="info-label">Event</span>
                                <span class="info-value">
                                    <code class="text-primary" data-view-style="admin-monitoring-show-1">
                                        {{ $log->event }}
                                    </code>
                                </span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Level</span>
                                <span class="info-value">
                                    <span class="badge bg-{{ $log->level_color }}-soft text-{{ $log->level_color }} px-3 py-2 rounded-pill">
                                        <i class="fas {{ $log->level_icon }} me-1"></i>
                                        {{ ucfirst($log->level) }}
                                    </span>
                                </span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Date & Time</span>
                                <span class="info-value">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold">{{ $log->created_at->format('F d, Y H:i:s') }}</span>
                                        <small class="text-secondary">{{ $log->created_at->diffForHumans() }}</small>
                                    </div>
                                </span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Description</span>
                                <span class="info-value">{{ $log->description }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mt-4">
                        <div class="card-header-custom">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-globe text-primary me-2"></i>Request Information
                            </h5>
                        </div>
                        <div class="card-body-custom">

                            <div class="info-row">
                                <span class="info-label">IP Address</span>
                                <span class="info-value">
                                    <code data-view-style="admin-monitoring-show-2">
                                        {{ $log->ip_address ?? '—' }}
                                    </code>
                                </span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">Method</span>
                                <span class="info-value">
                                    @if($log->method)
                                        <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill">{{ $log->method }}</span>
                                    @else
                                        <span class="text-secondary">—</span>
                                    @endif
                                </span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">URL</span>
                                <span class="info-value">
                                    @if($log->url)
                                        <code data-view-style="admin-monitoring-show-3">{{ $log->url }}</code>
                                    @else
                                        <span class="text-secondary">—</span>
                                    @endif
                                </span>
                            </div>

                            <div class="info-row">
                                <span class="info-label">User Agent</span>
                                <span class="info-value">
                                    @if($log->user_agent)
                                        <small data-view-style="admin-monitoring-show-4">{{ $log->user_agent }}</small>
                                    @else
                                        <span class="text-secondary">—</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($log->metadata && count($log->metadata) > 0)
                        <div class="card shadow-sm border-0 rounded-4 overflow-hidden mt-4">
                            <div class="card-header-custom">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-code text-primary me-2"></i>Additional Metadata
                                </h5>
                            </div>
                            <div class="card-body-custom">
                                <pre data-view-style="admin-monitoring-show-5">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">

                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                        <div class="card-header-custom">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-user text-primary me-2"></i>User
                            </h5>
                        </div>
                        <div class="card-body-custom">
                            @if($log->user)

                                <div class="text-center mb-3">
                                    @if($log->user->avatar)
                                        <img src="{{ asset('storage/' . $log->user->avatar) }}"
                                            class="rounded-circle mb-3"
                                            width="80" height="80"
                                            data-view-style="admin-monitoring-show-6">
                                    @else
                                        <div class="avatar-large bg-primary-soft text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                                data-view-style="admin-monitoring-show-7">
                                            {{ strtoupper(substr($log->user->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <h5 class="fw-bold mb-1">{{ $log->user->name }}</h5>
                                    <p class="text-secondary small mb-2">{{ $log->user->email }}</p>
                                    <span class="badge bg-{{ $log->user->isAdmin() ? 'primary' : 'secondary' }}-soft text-{{ $log->user->isAdmin() ? 'primary' : 'secondary' }} px-3 py-2 rounded-pill">
                                        <i class="fas {{ $log->user->isAdmin() ? 'fa-crown' : 'fa-user' }} me-1"></i>
                                        {{ $log->user->isAdmin() ? 'Admin' : 'User' }}
                                    </span>
                                </div>

                                <div class="d-grid gap-2 mt-4">
                                    <a href="{{ route('admin.users.edit', $log->user->id) }}" class="btn btn-outline-primary rounded-pill">
                                        <i class="fas fa-user-edit me-2"></i>View User
                                    </a>
                                </div>
                            @else

                                <div class="text-center py-4">
                                    <div class="avatar-large bg-secondary-soft text-secondary fw-bold rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                                            data-view-style="admin-monitoring-show-8">
                                        <i class="fas fa-user-secret"></i>
                                    </div>
                                    <h5 class="fw-bold mb-1">Guest</h5>
                                    <p class="text-secondary small">Not authenticated</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mt-4">
                        <div class="card-header-custom">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-tools text-primary me-2"></i>Actions
                            </h5>
                        </div>
                        <div class="card-body-custom">
                            <div class="d-grid gap-2">

                                <button type="button"
                                        class="btn btn-outline-primary rounded-pill"
                                        data-view-action="admin-monitoring-show-1" data-argument-0="{{ $log->ip_address ?? '' }}">
                                    <i class="fas fa-copy me-2"></i>Copy IP
                                </button>

                                @if($log->ip_address)
                                    <a href="https://www.ipaddress.com/ip/{{ $log->ip_address }}"
                                            target="_blank"
                                            class="btn btn-outline-secondary rounded-pill">
                                        <i class="fas fa-search me-2"></i>Lookup IP
                                    </a>
                                @endif

                                <a href="{{ route('admin.monitoring.index', ['level' => $log->level]) }}"
                                        class="btn btn-outline-info rounded-pill">
                                    <i class="fas fa-filter me-2"></i>Filter by Level
                                </a>

                                <a href="{{ route('admin.monitoring.index', ['event' => $log->event]) }}"
                                        class="btn btn-outline-info rounded-pill">
                                    <i class="fas fa-filter me-2"></i>Filter by Event
                                </a>
                            </div>
                        </div>
                    </div>

                    @php
                        $relatedLogs = \App\Models\ActivityLog::where('id', '!=', $log->id)
                            ->where(function($q) use ($log) {
                                if ($log->user_id) {
                                    $q->where('user_id', $log->user_id);
                                }
                                if ($log->ip_address) {
                                    $q->orWhere('ip_address', $log->ip_address);
                                }
                            })
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp

                    @if($relatedLogs->count() > 0)
                        <div class="card shadow-sm border-0 rounded-4 overflow-hidden mt-4">
                            <div class="card-header-custom">
                                <h5 class="fw-bold mb-0">
                                    <i class="fas fa-history text-primary me-2"></i>Related Activity
                                </h5>
                            </div>
                            <div class="card-body-custom p-0">
                                @foreach($relatedLogs as $related)
                                    <a href="{{ route('admin.monitoring.show', $related->id) }}"
                                            class="related-log-item">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-{{ $related->level_color }}-soft text-{{ $related->level_color }}" data-view-style="admin-monitoring-show-9">
                                                <i class="fas {{ $related->level_icon }}"></i>
                                            </span>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold small">{{ $related->event }}</div>
                                                <small class="text-secondary">{{ $related->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <input type="hidden" data-page-script="admin/monitoring/show">
@endsection
