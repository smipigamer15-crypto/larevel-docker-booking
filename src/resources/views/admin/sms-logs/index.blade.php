@extends('layouts.admin')
@section('style-page', 'admin/sms-logs/index')

@section('title', 'SMS Logs')

@section('content')
    <div class="admin-security py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-sms text-primary me-2"></i>SMS Logs
                    </h1>
                    <p class="text-secondary mb-0">History of all sent SMS notifications</p>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 ps-4">#</th>
                                    <th class="py-3">Phone Number</th>
                                    <th class="py-3">Message</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td class="ps-4 fw-semibold">#{{ $log->id }}</td>
                                        <td>
                                            <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill">
                                                <i class="fas fa-phone me-1"></i> {{ $log->phone }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-truncate" data-view-style="admin-sms-logs-index-1" title="{{ $log->message }}">
                                                {{ $log->message }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $log->status === 'sent' ? 'success' : 'danger' }}-soft text-{{ $log->status === 'sent' ? 'success' : 'danger' }} px-3 py-2 rounded-pill">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold small">{{ $log->created_at->format('M d, Y') }}</span>
                                                <small class="text-secondary">{{ $log->created_at->format('H:i') }}</small>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <i class="fas fa-inbox text-secondary display-4 d-block mb-3"></i>
                                            <h5 class="text-secondary">No SMS logs found</h5>
                                            <p class="text-secondary small">Sent messages will appear here</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($logs->hasPages())
                <div class="pagination-wrapper mt-4">
                    {{ $logs->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>
    </div>

@endsection
