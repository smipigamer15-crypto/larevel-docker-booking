@extends('layouts.admin')
@section('style-page', 'admin/security/show')

@section('title', 'Security Inquiry #' . $inquiry->id)

@section('content')
    <div class="admin-security-show py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-shield-alt text-primary me-2"></i>Security Inquiry #{{ $inquiry->id }}
                    </h1>
                    <p class="text-secondary mb-0">View & manage security package request</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge-total">
                        <i class="fas fa-hashtag"></i>ID: {{ $inquiry->id }}
                    </span>
                    <a href="{{ route('admin.security.index') }}" class="btn-clear">
                        <i class="fas fa-arrow-left"></i>Back
                    </a>
                </div>
            </div>

            <div class="row g-4">

                <div class="col-md-6">

                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="fw-bold mb-0">Client Information</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Name:</strong> {{ $inquiry->name }}</p>
                            <p><strong>Email:</strong> <a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a></p>
                            <p><strong>Phone:</strong> {{ $inquiry->phone ?? 'Not provided' }}</p>
                            <p><strong>Company:</strong> {{ $inquiry->company ?? 'Not provided' }}</p>
                            <p><strong>Package:</strong> <span class="badge bg-primary">{{ $inquiry->package_label }}</span></p>
                            <p><strong>Status:</strong> <span class="badge bg-{{ $inquiry->status_color }}">{{ $inquiry->status_label }}</span></p>
                            <p><strong>Duration:</strong> {{ $inquiry->duration_label }}</p>
                            <p><strong>Date:</strong> {{ $inquiry->created_at->format('F d, Y H:i') }}</p>

                            @if($inquiry->is_active)
                                <p><strong>Expires:</strong> <span class="fw-bold">{{ $inquiry->expires_at ? $inquiry->expires_at->format('F d, Y H:i') : 'N/A' }}</span></p>
                            @endif
                        </div>
                    </div>

                    <div class="card shadow-sm mt-4">
                        <div class="card-header bg-white">
                            <h5 class="fw-bold mb-0">Message</h5>
                        </div>
                        <div class="card-body">
                            <p>{{ $inquiry->message ?? 'No message provided' }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">

                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-shield-alt me-2 text-primary"></i>Access & Activation
                            </h5>
                            <div class="d-flex gap-2">
                                @if($inquiry->access_token)

                                    <form action="{{ route('admin.security.regenerate-token', $inquiry->id) }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-action-header" title="Regenerate Token" data-view-action="admin-security-show-1" >
                                            <i class="fas fa-sync-alt me-1"></i>Regenerate
                                        </button>
                                    </form>

                                    <form action="{{ route('admin.security.resend-link', $inquiry->id) }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-action-header btn-action-header-primary" title="Resend Link to Client">
                                            <i class="fas fa-envelope me-1"></i>Resend
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">

                            @if($inquiry->is_active)
                                <div class="alert alert-success rounded-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle fs-4 me-3"></i>
                                        <div>
                                            <strong>Access Activated!</strong>
                                            <p class="mb-0 text-secondary small">
                                                Activated on {{ $inquiry->access_activated_at->format('M d, Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <p class="fw-semibold mb-2">
                                        <i class="fas fa-shield-alt me-2 text-primary"></i>Active Security Features:
                                        <span class="badge bg-primary-soft text-primary ms-2">
                                            {{ $inquiry->active_features_count }} / {{ $inquiry->total_features_count }}
                                        </span>
                                    </p>
                                    <div class="row g-2">
                                        @php
                                            $features = $inquiry->features;
                                            $labels = [
                                                'ssl_encryption' => 'SSL Encryption',
                                                'two_factor_auth' => '2FA Authentication',
                                                'monitoring' => '24/7 Monitoring',
                                                'daily_backups' => 'Daily Backups',
                                                'advanced_firewall' => 'Advanced Firewall',
                                                'ddos_protection' => 'DDoS Protection',
                                                'vulnerability_scan' => 'Vulnerability Scan',
                                                'priority_support' => 'Priority Support',
                                                'api_access' => 'API Access',
                                                'custom_rules' => 'Custom Rules',
                                            ];
                                            $icons = [
                                                'ssl_encryption' => 'fa-shield-alt',
                                                'two_factor_auth' => 'fa-fingerprint',
                                                'monitoring' => 'fa-eye',
                                                'daily_backups' => 'fa-database',
                                                'advanced_firewall' => 'fa-shield',
                                                'ddos_protection' => 'fa-shield-virus',
                                                'vulnerability_scan' => 'fa-search',
                                                'priority_support' => 'fa-headset',
                                                'api_access' => 'fa-code',
                                                'custom_rules' => 'fa-sliders-h',
                                            ];
                                        @endphp

                                        @foreach($features as $key => $value)
                                            @if(isset($labels[$key]))
                                                <div class="col-md-6">
                                                    <div class="feature-item d-flex align-items-center p-2 rounded-3 {{ $value ? 'bg-success-soft' : 'bg-secondary-soft' }}">
                                                        <i class="fas {{ $icons[$key] ?? 'fa-circle' }} {{ $value ? 'text-success' : 'text-secondary' }} me-2"></i>
                                                        <span class="small {{ $value ? 'fw-semibold' : 'text-secondary' }}">
                                                            {{ $labels[$key] }}
                                                        </span>
                                                        <span class="ms-auto badge {{ $value ? 'bg-success' : 'bg-secondary' }}">
                                                            {{ $value ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mt-3 pt-3 border-top">
                                    <form action="{{ route('admin.security.deactivate-client', $inquiry->id) }}" method="POST" data-view-action="admin-security-show-2" >
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-danger rounded-pill px-4">
                                            <i class="fas fa-ban me-2"></i>Deactivate Client
                                        </button>
                                        <small class="text-secondary ms-2">This will remove access and send notification to client</small>
                                    </form>
                                </div>

                            @elseif($inquiry->access_token)
                                <div class="alert alert-info rounded-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-link fs-4 me-3 text-primary"></i>
                                        <div>
                                            <strong>Access Token Generated</strong>
                                            <p class="mb-0 text-secondary small">
                                                Token created: {{ $inquiry->created_at->format('M d, Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label class="fw-semibold mb-2">
                                        <i class="fas fa-external-link-alt me-1"></i>Client Access Link:
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="{{ route('security.client.status', $inquiry->access_token) }}" readonly id="clientLink">
                                        <button class="btn btn-outline-primary rounded-end" data-view-action="admin-security-show-3"  title="Copy link">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <a href="{{ route('security.client.status', $inquiry->access_token) }}" target="_blank" class="btn btn-primary rounded-end" title="Open in new tab">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </div>

                            @else
                                <div class="alert alert-secondary rounded-3">
                                    <i class="fas fa-hourglass-half me-2"></i>
                                    <strong>No token generated yet.</strong>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card shadow-sm mt-4">
                        <div class="card-header bg-white">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-cog me-2 text-primary"></i>Manage Access
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Status</label>
                                    <select class="form-select" id="statusSelect">
                                        <option value="pending" {{ $inquiry->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="contacted" {{ $inquiry->status == 'contacted' ? 'selected' : '' }}>Contacted</option>
                                        <option value="approved" {{ $inquiry->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ $inquiry->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="deactivated" {{ $inquiry->status == 'deactivated' ? 'selected' : '' }}>Deactivated</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold">Admin Notes</label>
                                    <textarea class="form-control" id="adminNotes" rows="2">{{ $inquiry->admin_notes }}</textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Expires At</label>
                                    <input type="datetime-local" class="form-control" id="expiresAtInput"
                                        value="{{ $inquiry->expires_at ? $inquiry->expires_at->format('Y-m-d\TH:i') : now()->addYear()->format('Y-m-d\TH:i') }}">
                                    <small class="text-secondary">Select exact expiration date & time</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Quick Set</label>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill quick-date" data-days="30">+30 days</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill quick-date" data-days="90">+90 days</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill quick-date" data-days="180">+180 days</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill quick-date" data-days="365">+365 days</button>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="sendEmailCheck" checked>
                                        <label class="form-check-label" for="sendEmailCheck">
                                            Send access link to client email
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="button" class="btn btn-primary rounded-pill px-4" id="manageBtn">
                                        <i class="fas fa-save me-2"></i>Save & Send Link
                                    </button>
                                    <small class="text-secondary ms-2">This will update status, expiration and send the link</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="view-data-admin-security-show-0" value="{{ $inquiry->is_active ? route("admin.security.update-duration", $inquiry->id) : route("admin.security.activate-client", $inquiry->id) }}">
    <input type="hidden" id="view-data-admin-security-show-1" value="{{ csrf_token() }}">
    <input type="hidden" data-page-script="admin/security/show">

@endsection
