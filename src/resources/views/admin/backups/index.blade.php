@extends('layouts.admin')
@section('style-page', 'admin/backups/index')

@section('title', 'Backups')

@section('content')
    <div class="admin-security py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-database text-primary me-2"></i>Daily Backups
                    </h1>
                    <p class="text-secondary mb-0">Automatic daily backups of your database and files</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge-total">
                        <i class="fas fa-hdd"></i>{{ count($backups) }} Backups
                    </span>
                    <form method="POST" action="{{ route('admin.backups.create') }}" class="d-inline m-0">
                        @csrf
                        <button type="submit" class="btn-clear">
                            <i class="fas fa-plus"></i>Create Backup
                        </button>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success rounded-3 border-0 mb-3">
                    <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger rounded-3 border-0 mb-3">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ session('error') }}
                </div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-primary-soft mb-2">
                            <i class="fas fa-database text-primary"></i>
                        </div>
                        <h3 class="fw-bold text-primary mb-0">{{ count($backups) }}</h3>
                        <p class="small text-secondary mb-0">Total Backups</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-success-soft mb-2">
                            <i class="fas fa-hdd text-success"></i>
                        </div>
                        <h3 class="fw-bold text-success mb-0">
                            {{ number_format($totalSize / 1024 / 1024, 1) }} MB
                        </h3>
                        <p class="small text-secondary mb-0">Total Size</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-info-soft mb-2">
                            <i class="fas fa-clock text-info"></i>
                        </div>
                        <h3 class="fw-bold text-info mb-0">03:00</h3>
                        <p class="small text-secondary mb-0">Daily Schedule</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-warning-soft mb-2">
                            <i class="fas fa-sync-alt text-warning"></i>
                        </div>
                        <form method="POST" action="{{ route('admin.backups.clean') }}" class="w-100">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm w-100" data-view-action="admin-backups-index-1" >
                                <i class="fas fa-broom me-1"></i>Clean Old
                            </button>
                        </form>
                        <p class="small text-secondary mb-0 mt-1">Free up space</p>
                    </div>
                </div>
            </div>

            <div class="alert alert-info rounded-4 d-flex align-items-start">
                <i class="fas fa-info-circle fs-4 me-3 mt-1"></i>
                <div>
                    <strong>Automatic Backups</strong>
                    <p class="mb-0 small">
                        Backups are created automatically every day at <strong>03:00 AM</strong>.
                        Old backups are cleaned up based on retention policy:
                        <strong>7 days</strong> (daily), <strong>8 weeks</strong> (weekly),
                        <strong>6 months</strong> (monthly).
                    </p>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 ps-4">#</th>
                                    <th class="py-3">Filename</th>
                                    <th class="py-3">Size</th>
                                    <th class="py-3">Created</th>
                                    <th class="py-3 text-center pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($backups as $index => $backup)
                                    <tr>
                                        <td class="ps-4 fw-semibold">{{ $index + 1 }}</td>

                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar bg-primary-soft text-primary rounded-circle d-flex align-items-center justify-content-center"
                                                        data-view-style="admin-backups-index-1">
                                                    <i class="fas fa-file-archive"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold small">{{ $backup['name'] }}</div>
                                                    <small class="text-secondary">
                                                        {{ Str::limit($backup['path'], 40) }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill">
                                                {{ $backup['size_human'] }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold small">{{ $backup['created_at_formatted'] }}</span>
                                                <small class="text-secondary">
                                                    {{ \Carbon\Carbon::createFromTimestamp($backup['created_at'])->diffForHumans() }}
                                                </small>
                                            </div>
                                        </td>

                                        <td class="text-center pe-4">
                                            <div class="d-flex gap-1 justify-content-center">
                                                <a href="{{ route('admin.backups.download', $backup['name']) }}"
                                                        class="btn-action btn-action-download"
                                                        title="Download">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.backups.delete', $backup['name']) }}"
                                                        class="d-inline"
                                                        data-view-action="admin-backups-index-2" >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action btn-action-delete" title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="fas fa-database text-secondary display-4 d-block mb-3"></i>
                                            <h5 class="text-secondary">No backups yet</h5>
                                            <p class="text-secondary small">Click "Create Backup" to make your first backup</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection
<input type="hidden" data-page-script="admin/backups/index">
