@extends('layouts.admin')
@section('style-page', 'admin/dashboard')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="admin-dashboard py-4">
        <div class="container">

            <div class="admin-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold mb-0">Dashboard</h1>
                    <p class="text-secondary mb-0">Welcome back, {{ auth()->user()->name }}!</p>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon" data-view-style="admin-dashboard-1">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number">{{ $totalBookings }}</span>
                            <span class="stat-label">Total Bookings</span>
                        </div>
                        <div class="stat-trend up">
                            <i class="fas fa-arrow-up"></i> 12%
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon" data-view-style="admin-dashboard-2">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number">{{ $totalUsers }}</span>
                            <span class="stat-label">Total Users</span>
                        </div>
                        <div class="stat-trend up">
                            <i class="fas fa-arrow-up"></i> 8%
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon" data-view-style="admin-dashboard-3">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number">{{ number_format($revenue, 2) }} $</span>
                            <span class="stat-label">Revenue</span>
                        </div>
                        <div class="stat-trend up">
                            <i class="fas fa-arrow-up"></i> 15%
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card">
                        <div class="stat-icon" data-view-style="admin-dashboard-4">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-number">{{ $totalSecurityInquiries ?? 0 }}</span>
                            <span class="stat-label">Security Requests</span>
                        </div>
                        <div class="stat-trend up">
                            <i class="fas fa-shield-alt me-1"></i>
                            @if(isset($pendingSecurity) && $pendingSecurity > 0)
                                <span class="text-danger">{{ $pendingSecurity }} pending</span>
                            @else
                                <span class="text-success">0 pending</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-12">
                    <div class="quick-actions d-flex flex-wrap gap-2">

                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                            <i class="fas fa-calendar-check me-2"></i>Manage Bookings
                            @if(($badges['bookings'] ?? 0) > 0)
                                <span class="badge badge-notification">{{ $badges['bookings'] }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                            <i class="fas fa-crown me-2"></i>Manage Subscriptions
                            @if(($badges['subscriptions'] ?? 0) > 0)
                                <span class="badge badge-notification">{{ $badges['subscriptions'] }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.services.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                            <i class="fas fa-concierge-bell me-2"></i>Manage Services
                            @if(($badges['services'] ?? 0) > 0)
                                <span class="badge badge-notification-warning">{{ $badges['services'] }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                            <i class="fas fa-users me-2"></i>Manage Users
                            @if(($badges['users'] ?? 0) > 0)
                                <span class="badge badge-notification-success">{{ $badges['users'] }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.security.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                            <i class="fas fa-shield-alt me-2"></i>Security Requests
                            @if(($badges['security'] ?? 0) > 0)
                                <span class="badge badge-notification">{{ $badges['security'] }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.monitoring.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                            <i class="fas fa-eye me-2"></i>24/7 Monitoring
                            @if(($badges['monitoring'] ?? 0) > 0)
                                <span class="badge badge-notification">{{ $badges['monitoring'] }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.backups.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                            <i class="fas fa-database me-2"></i>Backups
                        </a>

                        <a href="{{ route('admin.firewall.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                            <i class="fas fa-shield-virus me-2"></i>Firewall
                            @if(($badges['firewall'] ?? 0) > 0)
                                <span class="badge badge-notification">{{ $badges['firewall'] }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.vulnerability.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                            <i class="fas fa-shield-virus me-2"></i>Vulnerability Scan
                            @if(($badges['vulnerability'] ?? 0) > 0)
                                <span class="badge badge-notification">{{ $badges['vulnerability'] }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.api-tokens.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                            <i class="fas fa-key me-2"></i>API Access
                            @if(($badges['api'] ?? 0) > 0)
                                <span class="badge badge-notification-success">{{ $badges['api'] }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.custom-rules.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                            <i class="fas fa-sliders-h me-2"></i>Custom Rules
                            @if(($badges['custom_rules'] ?? 0) > 0)
                                <span class="badge badge-notification-success">{{ $badges['custom_rules'] }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.sms-logs.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                            <i class="fas fa-sms me-2"></i>SMS Logs
                            @if(($badges['sms_logs'] ?? 0) > 0)
                                <span class="badge badge-notification-success">{{ $badges['sms_logs'] }}</span>
                            @endif
                        </a>

                        <a href="{{ route('admin.support.index') }}" class="btn btn-outline-primary rounded-pill px-4">
                            <i class="fas fa-headset me-2"></i>Support Tickets
                            @if(($badges['support'] ?? 0) > 0)
                                <span class="badge badge-notification">{{ $badges['support'] }}</span>
                            @endif
                        </a>

                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-lg-8">
                    <div class="admin-card">
                        <div class="card-header-custom">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-chart-line text-primary me-2"></i>Bookings & Registrations
                            </h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.dashboard', ['days' => 7]) }}" class="btn btn-sm rounded-pill {{ $selectedDays === 7 ? 'btn-primary' : 'btn-outline-secondary' }}" @if($selectedDays === 7) aria-current="true" @endif>7d</a>
                                <a href="{{ route('admin.dashboard', ['days' => 30]) }}" class="btn btn-sm rounded-pill {{ $selectedDays === 30 ? 'btn-primary' : 'btn-outline-secondary' }}" @if($selectedDays === 30) aria-current="true" @endif>30d</a>
                                <a href="{{ route('admin.dashboard', ['days' => 90]) }}" class="btn btn-sm rounded-pill {{ $selectedDays === 90 ? 'btn-primary' : 'btn-outline-secondary' }}" @if($selectedDays === 90) aria-current="true" @endif>90d</a>
                            </div>
                        </div>
                        <div class="card-body-custom admin-chart">
                            <canvas id="bookingChart" height="80"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="admin-card h-100">
                        <div class="card-header-custom">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-chart-pie text-primary me-2"></i>Booking Status
                            </h5>
                        </div>
                        <div class="card-body-custom">
                            <div class="status-stats">
                                <div class="status-item">
                                    <span class="status-label">Upcoming</span>
                                    <div class="status-bar" data-view-style="admin-dashboard-5" data-view-width="{{ ($statusStats['upcoming'] / max($totalBookings, 1)) * 100 }}"></div>
                                    <span class="status-count">{{ $statusStats['upcoming'] }}</span>
                                </div>
                                <div class="status-item">
                                    <span class="status-label">Completed</span>
                                    <div class="status-bar" data-view-style="admin-dashboard-6" data-view-width="{{ ($statusStats['completed'] / max($totalBookings, 1)) * 100 }}"></div>
                                    <span class="status-count">{{ $statusStats['completed'] }}</span>
                                </div>
                                <div class="status-item">
                                    <span class="status-label">Cancelled</span>
                                    <div class="status-bar" data-view-style="admin-dashboard-7" data-view-width="{{ ($statusStats['cancelled'] / max($totalBookings, 1)) * 100 }}"></div>
                                    <span class="status-count">{{ $statusStats['cancelled'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-lg-6">
                    <div class="admin-card">
                        <div class="card-header-custom">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-fire text-warning me-2"></i>Popular Services
                            </h5>
                        </div>
                        <div class="card-body-custom">
                            @foreach($popularServices as $service)
                                <div class="popular-item">
                                    <div class="popular-info">
                                        <span class="fw-semibold">{{ $service->title }}</span>
                                        <span class="text-secondary small">{{ $service->bookings_count }} bookings</span>
                                    </div>
                                    <span class="popular-value">{{ number_format($service->price, 2) }} $</span>
                                </div>
                            @endforeach
                            @if($popularServices->isEmpty())
                                <div class="text-center py-3 text-secondary">No services yet</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="admin-card">
                        <div class="card-header-custom">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-star text-warning me-2"></i>Recent Reviews
                            </h5>
                        </div>
                        <div class="card-body-custom">
                            @foreach($recentReviews as $review)
                                <div class="review-item-mini p-2 mb-2 rounded-3" data-view-style="admin-dashboard-8">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-semibold small">{{ $review->user->name }}</div>
                                            <div class="text-warning small">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                            </div>
                                            <p class="text-secondary small mb-0">{{ Str::limit($review->comment, 60) }}</p>
                                        </div>
                                        <span class="badge bg-primary rounded-pill small">{{ $review->service->title }}</span>
                                    </div>
                                </div>
                            @endforeach
                            @if($recentReviews->isEmpty())
                                <div class="text-center py-3 text-secondary">No reviews yet</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-lg-6">
                    <div class="admin-card">
                        <div class="card-header-custom">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-clock text-primary me-2"></i>Recent Bookings
                            </h5>
                            <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                View All <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="card-body-custom p-0">
                            @foreach($recentBookings as $booking)
                                <div class="recent-item">
                                    <div class="recent-info">
                                        <span class="fw-semibold">{{ $booking->service->title }}</span>
                                        <span class="text-secondary small">
                                            {{ $booking->user->name }} · {{ $booking->booking_date->format('d.m.Y') }}
                                        </span>
                                    </div>
                                    <span class="status-badge
                                            @if($booking->status == 'upcoming') status-upcoming
                                            @elseif($booking->status == 'completed') status-completed
                                            @else status-cancelled
                                            @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                            @endforeach
                            @if($recentBookings->isEmpty())
                                <div class="text-center py-4 text-secondary">No bookings yet</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="admin-card">
                        <div class="card-header-custom">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-shield-alt text-purple me-2"></i>Security Inquiries
                            </h5>
                            <a href="{{ route('admin.security.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                View All <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="card-body-custom p-0">
                            @if(isset($recentSecurityInquiries) && $recentSecurityInquiries->count() > 0)
                                @foreach($recentSecurityInquiries as $inquiry)
                                    <div class="recent-item">
                                        <div class="recent-info">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="bg-purple-soft text-purple rounded-circle d-flex align-items-center justify-content-center" data-view-style="admin-dashboard-9">
                                                    <i class="fas fa-shield-alt"></i>
                                                </div>
                                                <div>
                                                    <span class="fw-semibold">{{ $inquiry->name }}</span>
                                                    <span class="text-secondary small d-block">{{ $inquiry->email }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-{{ $inquiry->status_color }} rounded-pill">
                                                {{ $inquiry->status_label }}
                                            </span>
                                            <a href="{{ route('admin.security.show', $inquiry->id) }}" class="btn btn-sm btn-outline-primary rounded-pill" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4 text-secondary">
                                    <i class="fas fa-shield-alt fs-2 d-block mb-2 opacity-25"></i>
                                    No security inquiries yet
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <div class="admin-card">
                        <div class="card-header-custom">
                            <h5 class="fw-bold mb-0">
                                <i class="fas fa-users text-primary me-2"></i>Recent Users
                            </h5>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                View All <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="card-body-custom p-0">
                            <div class="row g-0">
                                @foreach($recentUsers as $user)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="recent-item">
                                            <div class="recent-info">
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($user->avatar)
                                                        <img src="{{ asset('storage/' . $user->avatar) }}" class="rounded-circle" width="32" height="32" alt="{{ $user->name }}">
                                                    @else
                                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" data-view-style="admin-dashboard-10">
                                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <span class="fw-semibold">{{ $user->name }}</span>
                                                        <span class="text-secondary small d-block">{{ $user->email }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="role-badge {{ $user->isAdmin() ? 'admin' : 'user' }}">
                                                {{ $user->isAdmin() ? 'Admin' : 'User' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if($recentUsers->isEmpty())
                                <div class="text-center py-4 text-secondary">No users yet</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <input type="hidden" id="view-data-admin-dashboard-0" value="{{ json_encode($chartData) }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <input type="hidden" data-page-script="admin/dashboard">
@endsection
