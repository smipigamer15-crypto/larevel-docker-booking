<div class="profile-form-card card-modern p-4 p-md-5 mb-4">
    <div class="form-card-header">
        <h5 class="fw-bold mb-1">
            <i class="fas fa-shield-alt text-primary me-2"></i>{{ __('Security Status') }}
        </h5>
        <p class="text-secondary small mb-0">{{ __('View the status of your security package') }}</p>
    </div>

    @if($securityInquiry)
        <div class="security-status-wrapper">
            <div class="security-header d-flex align-items-center gap-3 mb-4">
                <div class="security-icon-circle">
                    @if($securityInquiry->is_active)
                        <i class="fas fa-shield-alt"></i>
                    @elseif($securityInquiry->status == 'pending')
                        <i class="fas fa-clock"></i>
                    @elseif($securityInquiry->status == 'approved')
                        <i class="fas fa-check-circle"></i>
                    @elseif($securityInquiry->status == 'deactivated')
                        <i class="fas fa-ban"></i>
                    @else
                        <i class="fas fa-info-circle"></i>
                    @endif
                </div>
                <div>
                    <h5 class="fw-bold mb-0">{{ $securityInquiry->package_label }}</h5>
                    <p class="text-secondary small mb-0">{{ $securityInquiry->package_description }}</p>
                    <div class="d-flex gap-2 mt-1">
                        <span class="badge-status status-{{ $securityInquiry->status ?? 'pending' }}">
                            {{ __($securityInquiry->status_label) }}
                        </span>
                        @if($securityInquiry->is_active)
                            <span class="badge-status status-active">
                                <i class="fas fa-check-circle me-1"></i>{{ __('Active') }}
                            </span>
                        @endif
                        @if($securityInquiry->package_price && $securityInquiry->package_price != __('Custom'))
                            <span class="badge-status bg-primary-soft text-primary">
                                ${{ $securityInquiry->package_price }}{{ __('/mo') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="security-grid">
                <div class="security-grid-item">
                    <span class="grid-label">{{ __('Connected Tariff') }}</span>
                    <span class="grid-value text-primary">
                        <i class="fas fa-crown me-1"></i>
                        {{ $securityInquiry->package_label }}
                    </span>
                </div>
                <div class="security-grid-item">
                    <span class="grid-label">{{ __('Status') }}</span>
                    <span class="grid-value">
                        <span class="badge-status status-{{ $securityInquiry->status ?? 'pending' }}">
                            {{ __($securityInquiry->status_label) }}
                        </span>
                        @if($securityInquiry->is_active)
                            <span class="badge-status status-active ms-1">
                                <i class="fas fa-check-circle me-1"></i>{{ __('Active') }}
                            </span>
                        @endif
                    </span>
                </div>
                <div class="security-grid-item">
                    <span class="grid-label">{{ __('Requested Date') }}</span>
                    <span class="grid-value">{{ $securityInquiry->created_at->translatedFormat('M d, Y') }}</span>
                </div>
                <div class="security-grid-item">
                    <span class="grid-label">{{ __('Activated Date') }}</span>
                    <span class="grid-value">
                        @if($securityInquiry->access_activated_at)
                            {{ $securityInquiry->access_activated_at->translatedFormat('M d, Y H:i') }}
                        @else
                            <span class="text-secondary">{{ __('Not activated') }}</span>
                        @endif
                    </span>
                </div>
            </div>

            @if($securityInquiry->is_active)
                <div class="security-settings mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">
                            <i class="fas fa-cog text-primary me-2"></i>{{ __('Security Settings') }}
                        </h6>
                        <span class="badge bg-primary-soft text-primary">
                            {{ $securityInquiry->active_features_count }} / {{ $securityInquiry->total_features_count }} {{ __('Active') }}
                        </span>
                    </div>
                    <div class="row g-2">
                        @php
                            $features = $securityInquiry->features;
                            $labels = [
                                'ssl_encryption' => __('SSL Encryption'),
                                'two_factor_auth' => '2FA Authentication',
                                'monitoring' => '24/7 Monitoring',
                                'daily_backups' => __('Daily Backups'),
                                'advanced_firewall' => __('Advanced Firewall'),
                                'ddos_protection' => __('DDoS Protection'),
                                'vulnerability_scan' => __('Vulnerability Scan'),
                                'priority_support' => __('Priority Support'),
                                'api_access' => __('API Access'),
                                'custom_rules' => __('Custom Rules'),
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
                                    <div class="setting-item d-flex justify-content-between align-items-center p-3 border rounded-3 {{ $value ? 'active' : 'inactive' }}">
                                        <span>
                                            <i class="fas {{ $icons[$key] ?? 'fa-circle' }} text-primary me-2"></i>
                                            {{ $labels[$key] }}
                                        </span>
                                        <span class="badge {{ $value ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $value ? __('Active') : __('Inactive') }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            @if($securityInquiry->status == 'deactivated')
                <div class="alert alert-secondary rounded-3 mt-3">
                    <i class="fas fa-ban me-2"></i>
                    <strong>{{ __('Your security package has been deactivated.') }}</strong>
                    <p class="mb-0 text-secondary small mt-1">
                        {{ __('Please contact support if you have any questions.') }}
                    </p>
                </div>
            @endif

            @if($securityInquiry->is_active && $securityInquiry->expires_at && $securityInquiry->expires_at->isFuture())
                <div class="security-expiration mt-3 p-3 rounded-3
                        @if($securityInquiry->days_remaining <= 3) expiring-danger
                        @elseif($securityInquiry->days_remaining <= 7) expiring-warning
                        @else expiring-success
                        @endif">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="fas fa-clock me-2"></i>
                            <span class="fw-semibold">{{ $securityInquiry->days_remaining }}</span> {{ __('days remaining') }}
                        </div>
                        @if($securityInquiry->days_remaining <= 3)
                            <span class="fw-bold text-danger">{{ __('Expires soon!') }}</span>
                        @endif
                    </div>
                </div>
            @endif

            @if($securityInquiry->access_token && $securityInquiry->status != 'deactivated')
                <div class="text-center mt-4">
                    <a href="{{ route('security.client.status', $securityInquiry->access_token) }}"
                            class="btn btn-primary rounded-pill px-5 py-2">
                        <i class="fas fa-external-link-alt me-2"></i>{{ __('View Full Status') }}
                    </a>
                </div>
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <i class="fas fa-shield-alt text-secondary fs-2 mb-2 d-block opacity-25"></i>
            <p class="text-secondary">{{ __('You don\'t have any security package requests.') }}</p>
            <a href="{{ route('security') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-shield-alt me-2"></i>{{ __('Request Security Package') }}
            </a>
        </div>
    @endif
</div>

<div class="profile-form-card card-modern p-4 p-md-5 mb-4">
    <div class="form-card-header">
        <h5 class="fw-bold mb-1">
            <i class="fas fa-crown text-primary me-2"></i>{{ __('Booking Subscription') }}
        </h5>
        <p class="text-secondary small mb-0">{{ __('View the status of your booking plan') }}</p>
    </div>

    @if($bookingSubscription)
        <div class="security-status-wrapper">
            <div class="security-header d-flex align-items-center gap-3 mb-4">
                <div class="security-icon-circle">
                    @if($bookingSubscription->is_active && !$bookingSubscription->cancel_at_period_end)
                        <i class="fas fa-crown"></i>
                    @elseif($bookingSubscription->cancel_at_period_end)
                        <i class="fas fa-clock"></i>
                    @elseif($bookingSubscription->status == 'pending')
                        <i class="fas fa-hourglass-half"></i>
                    @elseif($bookingSubscription->status == 'approved')
                        <i class="fas fa-check-circle"></i>
                    @elseif($bookingSubscription->status == 'deactivated')
                        <i class="fas fa-ban"></i>
                    @else
                        <i class="fas fa-info-circle"></i>
                    @endif
                </div>
                <div>
                    <h5 class="fw-bold mb-0">{{ $bookingSubscription->plan_label }} {{ __('Plan') }}</h5>
                    <p class="text-secondary small mb-0">
                        @if($bookingSubscription->package === 'pro')
                            {{ __('Best for growing businesses') }}
                        @elseif($bookingSubscription->package === 'enterprise' || $bookingSubscription->package === 'business')
                            {{ __('For large businesses') }}
                        @else
                            {{ __('Booking subscription') }}
                        @endif
                    </p>
                    <div class="d-flex gap-2 mt-1 flex-wrap">
                        <span class="badge-status status-{{ $bookingSubscription->status ?? 'pending' }}">
                            {{ __(ucfirst($bookingSubscription->status ?? 'pending')) }}
                        </span>
                        @if($bookingSubscription->is_active && !$bookingSubscription->cancel_at_period_end)
                            <span class="badge-status status-active">
                                <i class="fas fa-check-circle me-1"></i>{{ __('Active') }}
                            </span>
                        @endif
                        @if($bookingSubscription->cancel_at_period_end)
                            <span class="badge-status status-pending">
                                <i class="fas fa-clock me-1"></i>{{ __('Cancels soon') }}
                            </span>
                        @endif
                        <span class="badge-status bg-primary-soft text-primary">
                            ${{ $bookingSubscription->package === 'pro' ? '29' : '99' }}{{ __('/mo') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="security-grid">
                <div class="security-grid-item">
                    <span class="grid-label">{{ __('Plan') }}</span>
                    <span class="grid-value text-primary">
                        <i class="fas fa-crown me-1"></i>
                        {{ $bookingSubscription->plan_label }}
                    </span>
                </div>
                <div class="security-grid-item">
                    <span class="grid-label">{{ __('Status') }}</span>
                    <span class="grid-value">
                        @if($bookingSubscription->is_active && !$bookingSubscription->cancel_at_period_end)
                            <span class="badge-status status-active">
                                <i class="fas fa-check-circle me-1"></i>{{ __('Active') }}
                            </span>
                        @elseif($bookingSubscription->cancel_at_period_end)
                            <span class="badge-status status-pending">
                                <i class="fas fa-clock me-1"></i>{{ __('Cancelling') }}
                            </span>
                        @else
                            <span class="badge-status status-{{ $bookingSubscription->status ?? 'pending' }}">
                                {{ __(ucfirst($bookingSubscription->status ?? 'pending')) }}
                            </span>
                        @endif
                    </span>
                </div>
                <div class="security-grid-item">
                    <span class="grid-label">{{ __('Started') }}</span>
                    <span class="grid-value">
                        {{ $bookingSubscription->access_activated_at?->translatedFormat('M d, Y') ?? $bookingSubscription->created_at->translatedFormat('M d, Y') }}
                    </span>
                </div>
                <div class="security-grid-item">
                    <span class="grid-label">
                        @if($bookingSubscription->cancel_at_period_end)
                            {{ __('Ends on') }}
                        @else
                            {{ __('Next billing') }}
                        @endif
                    </span>
                    <span class="grid-value">
                        @if($bookingSubscription->subscription_ends_at)
                            {{ $bookingSubscription->subscription_ends_at->translatedFormat('M d, Y') }}
                        @else
                            <span class="text-secondary">—</span>
                        @endif
                    </span>
                </div>
            </div>

            @if($bookingSubscription->is_active)
                <div class="security-settings mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">
                            <i class="fas fa-gift text-primary me-2"></i>{{ __('What\'s included') }}
                        </h6>
                        <span class="badge bg-primary-soft text-primary">
                            {{ $bookingSubscription->package === 'pro' ? '5' : '5' }} {{ __('features') }}
                        </span>
                    </div>
                    <div class="row g-2">
                        @php
                            $planFeatures = $bookingSubscription->package === 'pro'
                                ? [
                                    ['icon' => 'fa-infinity', 'label' => __('Unlimited bookings')],
                                    ['icon' => 'fa-sms', 'label' => __('SMS notifications')],
                                    ['icon' => 'fa-headset', 'label' => '24/7 support'],
                                    ['icon' => 'fa-plug', 'label' => __('Additional integrations')],
                                    ['icon' => 'fa-chart-line', 'label' => __('Analytics & reports')],
                                ]
                                : [
                                    ['icon' => 'fa-check-double', 'label' => __('All Pro features')],
                                    ['icon' => 'fa-code', 'label' => __('API access')],
                                    ['icon' => 'fa-users-cog', 'label' => __('Multiple administrators')],
                                    ['icon' => 'fa-bolt', 'label' => __('Priority support')],
                                    ['icon' => 'fa-paint-brush', 'label' => __('White-label branding')],
                                ];
                        @endphp
                        @foreach($planFeatures as $feature)
                            <div class="col-md-6">
                                <div class="setting-item active d-flex justify-content-between align-items-center p-3 border rounded-3">
                                    <span>
                                        <i class="fas {{ $feature['icon'] }} text-primary me-2"></i>
                                        {{ $feature['label'] }}
                                    </span>
                                    <span class="badge bg-success">{{ __('Active') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($bookingSubscription->status == 'deactivated')
                <div class="alert alert-secondary rounded-3 mt-3">
                    <i class="fas fa-ban me-2"></i>
                    <strong>{{ __('Your booking subscription has been deactivated.') }}</strong>
                    <p class="mb-0 text-secondary small mt-1">
                        {{ __('Please contact support if you have any questions.') }}
                    </p>
                </div>
            @endif

            @if($bookingSubscription->is_active && $bookingSubscription->subscription_ends_at)
                @php
                    $daysLeft = (int) ceil(now()->diffInDays($bookingSubscription->subscription_ends_at, false));
                @endphp
                <div class="security-expiration mt-3 p-3 rounded-3
                        @if($bookingSubscription->cancel_at_period_end)
                            expiring-warning
                        @elseif($daysLeft <= 3)
                            expiring-danger
                        @elseif($daysLeft <= 7)
                            expiring-warning
                        @else
                            expiring-success
                        @endif">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <i class="fas fa-clock me-2"></i>
                            @if($bookingSubscription->cancel_at_period_end)
                                {{ __('Subscription ends in') }} <span class="fw-semibold">{{ $daysLeft }}</span> {{ __('days') }}
                            @else
                                <span class="fw-semibold">{{ $daysLeft }}</span> {{ __('days remaining') }}
                            @endif
                        </div>
                        @if($bookingSubscription->cancel_at_period_end)
                            <span class="fw-bold text-warning">{{ __('Cancelling') }}</span>
                        @endif
                    </div>
                </div>
            @endif

            @if($bookingSubscription->access_token)
                <div class="text-center mt-4">
                    <a href="{{ route('booking.subscription.manage', $bookingSubscription->access_token) }}"
                            class="btn btn-primary rounded-pill px-5 py-2">
                        <i class="fas fa-sliders-h me-2"></i>{{ __('Manage Subscription') }}
                    </a>
                </div>
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <i class="fas fa-crown text-secondary fs-2 mb-2 d-block opacity-25"></i>
            <p class="text-secondary">{{ __('You don\'t have an active booking subscription.') }}</p>
            <a href="{{ route('pricing') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-crown me-2"></i>{{ __('Choose a Plan') }}
            </a>
        </div>
    @endif
</div>
