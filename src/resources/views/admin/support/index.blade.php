@extends('layouts.admin')
@section('style-page', 'admin/support/index')

@section('title', 'Support Tickets')

@section('content')
    <div class="admin-security py-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold mb-1 d-flex align-items-center">
                        <i class="fas fa-headset text-primary me-3"></i>Support Tickets
                    </h1>
                    <p class="text-secondary mb-0">Manage user support requests</p>
                </div>
            </div>

            @if(session('success'))
                <div class="custom-alert mb-4" role="alert">
                    <div class="alert-content">
                        <i class="fas fa-check-circle alert-icon"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" class="alert-close" data-view-action="admin-support-index-1"  aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 ps-4 text-uppercase text-secondary" data-view-style="admin-support-index-1">#</th>
                                <th class="py-3 text-uppercase text-secondary" data-view-style="admin-support-index-2">User</th>
                                <th class="py-3 text-uppercase text-secondary" data-view-style="admin-support-index-3">Subject</th>
                                <th class="py-3 text-uppercase text-secondary" data-view-style="admin-support-index-4">Priority</th>
                                <th class="py-3 text-uppercase text-secondary" data-view-style="admin-support-index-5">Status</th>
                                <th class="py-3 text-uppercase text-secondary" data-view-style="admin-support-index-6">Date</th>
                                <th class="py-3 text-center pe-4 text-uppercase text-secondary" data-view-style="admin-support-index-7">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                                <tr>
                                    <td class="ps-4 fw-semibold text-muted">#{{ $ticket->id }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $ticket->user->name }}</div>
                                        <small class="text-secondary">{{ $ticket->user->email }}</small>
                                    </td>
                                    <td data-view-style="admin-support-index-8">
                                        <div class="fw-semibold text-truncate" title="{{ $ticket->subject }}">{{ $ticket->subject }}</div>
                                        <small class="text-secondary text-truncate d-block" title="{{ $ticket->message }}">{{ Str::limit($ticket->message, 50) }}</small>
                                    </td>
                                    <td>
                                        @if($ticket->is_priority)
                                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill d-inline-flex align-items-center">
                                                <i class="fas fa-bolt me-2"></i>HIGH
                                            </span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill d-inline-flex align-items-center">
                                                <i class="fas fa-circle me-2" data-view-style="admin-support-index-9"></i>Normal
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $ticket->status === 'open' ? 'warning' : 'success' }} bg-opacity-10 text-{{ $ticket->status === 'open' ? 'warning' : 'success' }} px-3 py-2 rounded-pill d-inline-flex align-items-center">
                                            <i class="fas fa-{{ $ticket->status === 'open' ? 'clock' : 'check-circle' }} me-2"></i>
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-secondary small">{{ $ticket->created_at->format('M d, Y') }}</span>
                                        <span class="text-muted small d-block">{{ $ticket->created_at->format('H:i') }}</span>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#replyModal{{ $ticket->id }}" title="Reply">
                                                <i class="fas fa-reply me-1"></i>Reply
                                            </button>

                                            @if($ticket->status === 'open')
                                                <form action="{{ route('admin.support.resolve', $ticket->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-success rounded-pill px-3" title="Resolve Ticket">
                                                        <i class="fas fa-check me-1"></i>Resolve
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="replyModal{{ $ticket->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 shadow-lg rounded-4">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="fw-bold mb-0">
                                                    <i class="fas fa-reply text-primary me-2"></i>Reply to {{ $ticket->user->name }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.support.reply', $ticket->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3 p-3 bg-light rounded-3">
                                                        <div class="fw-semibold text-dark mb-1">Subject: {{ $ticket->subject }}</div>
                                                        <div class="text-secondary small">{{ $ticket->message }}</div>
                                                        <div data-support-thread="{{ route('admin.support.messages', $ticket) }}">
                                                            @foreach($threads[$ticket->id] ?? [] as $message)
                                                                <div class="border-top mt-3 pt-3"><strong class="small">{{ $message['admin'] ? 'Support' : 'Customer' }}</strong><p class="small mb-0">{{ $message['body'] }}</p></div>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Your Response</label>
                                                        <textarea name="reply_message" class="form-control rounded-3" rows="6" placeholder="Type your response to the user here..." required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                                                        <i class="fas fa-paper-plane me-2"></i>Send Reply
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-secondary">
                                        <i class="fas fa-inbox fs-1 d-block mb-3 opacity-25"></i>
                                        No support tickets found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">{{ $tickets->links() }}</div>
        </div>
    </div>

@endsection
<input type="hidden" data-page-script="admin/support/index">
