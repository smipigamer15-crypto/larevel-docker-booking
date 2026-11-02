@extends('layouts.admin')
@section('style-page', 'admin/subscriptions/show')

@section('title', 'Booking Subscription #' . $subscription->id)

@section('content')
    <div class="admin-security-show py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-crown text-primary me-2"></i>Booking Subscription #{{ $subscription->id }}
                    </h1>
                    <p class="text-secondary mb-0">View & manage booking plan subscription</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge-total">
                        <i class="fas fa-hashtag"></i>ID: {{ $subscription->id }}
                    </span>
                    <a href="{{ route('admin.subscriptions.index') }}" class="btn-clear">
                        <i class="fas fa-arrow-left"></i>Back
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success rounded-3 mb-4">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger rounded-3 mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                </div>
            @endif

            <div class="row g-4">

                <div class="col-md-6">

                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="fw-bold mb-0">Client Information</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Name:</strong> {{ $subscription->name }}</p>
                            <p><strong>Email:</strong> <a href="mailto:{{ $subscription->email }}">{{ $subscription->email }}</a></p>
                            <p><strong>Phone:</strong> {{ $subscription->phone ?? 'Not provided' }}</p>
                            <p><strong>Company:</strong> {{ $subscription->company ?? 'Not provided' }}</p>
                            <p><strong>Plan:</strong>
                                <span class="badge bg-primary">{{ $subscription->plan_label }}</span>
                            </p>
                            <p><strong>Status:</strong>
                                @if($subscription->is_active && !$subscription->cancel_at_period_end)
                                    <span class="badge bg-success">Active</span>
                                @elseif($subscription->cancel_at_period_end)
                                    <span class="badge bg-warning">Cancelling</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($subscription->status) }}</span>
                                @endif
                            </p>
                            <p><strong>Created:</strong> {{ $subscription->created_at->format('F d, Y H:i') }}</p>
                            @if($subscription->access_activated_at)
                                <p><strong>Activated:</strong> {{ $subscription->access_activated_at->format('F d, Y H:i') }}</p>
                            @endif
                            @if($subscription->subscription_ends_at)
                            <p><strong>@if($subscription->cancel_at_period_end) Ends on @else Next billing @endif:</strong>
                                    <span class="fw-bold">{{ $subscription->subscription_ends_at->format('F d, Y') }}</span>
                                </p>
                                @if($subscription->is_active)
                                    <p><strong>Days remaining:</strong>
                                        <span class="fw-bold">{{ $subscription->days_remaining }}</span>
                                    </p>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="card shadow-sm mt-4">
                        <div class="card-header bg-white">
                            <h5 class="fw-bold mb-0">
                                <i class="fab fa-stripe text-primary me-2"></i>Stripe Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Customer ID:</strong> <code class="small">{{ $subscription->stripe_customer_id ?? '—' }}</code></p>
                            <p><strong>Subscription ID:</strong> <code class="small">{{ $subscription->stripe_subscription_id ?? '—' }}</code></p>
                            <p><strong>Status:</strong> {{ $subscription->subscription_status ?? '—' }}</p>
                            <p><strong>Cancel at period end:</strong> {{ $subscription->cancel_at_period_end ? 'Yes' : 'No' }}</p>
                        </div>
                    </div>

                </div>

                <div class="col-md-6">

                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-crown me-2 text-primary"></i>Access & Activation
                            </h5>
                            <div class="d-flex gap-2">
                                @if($subscription->access_token)
                                    <form action="{{ route('admin.subscriptions.regenerate-token', $subscription->id) }}" method="POST" class="d-inline m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-action-header" title="Regenerate Token"
                                                data-view-action="admin-subscriptions-show-1" >
                                            <i class="fas fa-sync-alt me-1"></i>Regenerate
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">

                            @if($subscription->is_active)
                                <div class="alert alert-success rounded-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle fs-4 me-3"></i>
                                        <div>
                                            <strong>Subscription Active!</strong>
                                            <p class="mb-0 text-secondary small">
                                                @if($subscription->access_activated_at)
                                                    Activated on {{ $subscription->access_activated_at->format('M d, Y H:i') }}
                                                @else
                                                    Currently active
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <p class="fw-semibold mb-2">
                                        <i class="fas fa-gift me-2 text-primary"></i>Included Features:
                                    </p>
                                    <div class="row g-2">
                                        @php
                                            $planFeatures = $subscription->package === 'pro'
                                                ? [
                                                    ['icon' => 'fa-infinity', 'label' => 'Unlimited bookings'],
                                                    ['icon' => 'fa-sms', 'label' => 'SMS notifications'],
                                                    ['icon' => 'fa-headset', 'label' => '24/7 support'],
                                                    ['icon' => 'fa-plug', 'label' => 'Additional integrations'],
                                                    ['icon' => 'fa-chart-line', 'label' => 'Analytics & reports'],
                                                ]
                                                : [
                                                    ['icon' => 'fa-check-double', 'label' => 'All Pro features'],
                                                    ['icon' => 'fa-code', 'label' => 'API access'],
                                                    ['icon' => 'fa-users-cog', 'label' => 'Multiple administrators'],
                                                    ['icon' => 'fa-bolt', 'label' => 'Priority support'],
                                                    ['icon' => 'fa-paint-brush', 'label' => 'White-label branding'],
                                                ];
                                        @endphp
                                        @foreach($planFeatures as $feature)
                                            <div class="col-md-6">
                                                <div class="feature-item d-flex align-items-center p-2 rounded-3 bg-success-soft">
                                                    <i class="fas {{ $feature['icon'] }} text-success me-2"></i>
                                                    <span class="small fw-semibold">{{ $feature['label'] }}</span>
                                                    <span class="ms-auto badge bg-success">Active</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mt-3 pt-3 border-top">
                                    <form action="{{ route('admin.subscriptions.deactivate', $subscription->id) }}" method="POST"
                                            data-view-action="admin-subscriptions-show-2" >
                                        @csrf
                                        <button type="submit" class="btn btn-danger rounded-pill px-4">
                                            <i class="fas fa-ban me-2"></i>Deactivate Subscription
                                        </button>
                                    </form>
                                </div>

                            @elseif($subscription->access_token)
                                <div class="alert alert-info rounded-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-link fs-4 me-3 text-primary"></i>
                                        <div>
                                            <strong>Access Token Generated</strong>
                                            <p class="mb-0 text-secondary small">
                                                Token created: {{ $subscription->created_at->format('M d, Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label class="fw-semibold mb-2">
                                        <i class="fas fa-external-link-alt me-1"></i>Client Access Link:
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control"
                                            value="{{ route('booking.subscription.manage', $subscription->access_token) }}"
                                            readonly id="clientLink">
                                        <button class="btn btn-outline-primary rounded-end" data-view-action="admin-subscriptions-show-3"  title="Copy link">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <a href="{{ route('booking.subscription.manage', $subscription->access_token) }}"
                                                target="_blank" class="btn btn-primary rounded-end" title="Open in new tab">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="mt-3 pt-3 border-top">
                                    <form action="{{ route('admin.subscriptions.activate', $subscription->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success rounded-pill px-4">
                                            <i class="fas fa-check me-2"></i>Activate Subscription
                                        </button>
                                    </form>
                                </div>

                            @else
                                <div class="alert alert-secondary rounded-3">
                                    <i class="fas fa-hourglass-half me-2"></i>
                                    <strong>No token generated yet.</strong>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <input type="hidden" data-page-script="admin/subscriptions/show">

@endsection
