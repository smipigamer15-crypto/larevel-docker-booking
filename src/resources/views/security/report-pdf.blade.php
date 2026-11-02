<!DOCTYPE html>
<html data-style-page="security/report-pdf">
    <head>
        <meta charset="utf-8">
        <title>Security Report #{{ $inquiry->id }}</title>

        {!! '<style>' . file_get_contents(resource_path('css/security.css')) . '</style>' !!}
    </head>
    <body>

        <div class="header">
            <div class="company-name">Booking System</div>
            <div class="report-label">Security Report</div>

            <div class="report-meta">
                <div>Report ID: <strong>#SEC-{{ str_pad($inquiry->id, 4, '0', STR_PAD_LEFT) }}</strong></div>
                <div>Date: <strong>{{ now()->translatedFormat('M d, Y') }}</strong></div>
            </div>
            <div data-view-style="security-report-pdf-1"></div>
        </div>

        <div class="title">
            <h1>SECURITY REPORT</h1>
            <p>Confidential documentation</p>
        </div>

        <table data-view-style="security-report-pdf-2">
            <tr>
                <td data-view-style="security-report-pdf-3">
                    <div class="badge {{ $inquiry->is_active ? 'active' : 'inactive' }}">
                        {{ $inquiry->is_active ? 'ACTIVE' : strtoupper($inquiry->status_label) }}
                    </div>
                </td>
            </tr>
        </table>

        <div class="section">
            <div class="section-title">Client Information</div>
            <table class="info-table">
                <tr>
                    <td>Name</td>
                    <td>{{ $inquiry->name }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{ $inquiry->email }}</td>
                </tr>
                @if($inquiry->phone)
                    <tr>
                        <td>Phone</td>
                        <td>{{ $inquiry->phone }}</td>
                    </tr>
                @endif
                @if($inquiry->company)
                    <tr>
                        <td>Company</td>
                        <td>{{ $inquiry->company }}</td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="section">
            <div class="section-title">Subscription Details</div>
            <table class="info-table">
                <tr>
                    <td>Package</td>
                    <td>{{ $inquiry->package_label }}</td>
                </tr>
                @if($inquiry->package_price && $inquiry->package_price != 'Custom')
                    <tr>
                        <td>Price</td>
                        <td>${{ $inquiry->package_price }} / month</td>
                    </tr>
                @endif
                @if($inquiry->access_activated_at)
                    <tr>
                        <td>Activated</td>
                        <td>{{ $inquiry->access_activated_at->translatedFormat('M d, Y') }}</td>
                    </tr>
                @endif
                @if($inquiry->expires_at)
                    <tr>
                        <td>Valid Until</td>
                        <td>{{ $inquiry->expires_at->translatedFormat('M d, Y') }} ({{ ceil($inquiry->days_remaining) }} days remaining)</td>
                    </tr>
                @endif
            </table>
        </div>

        <table class="stats-table">
            <tr>
                <td>
                    <span class="stat-value">{{ $inquiry->active_features_count }}</span>
                    <span class="stat-label">Active Features</span>
                </td>
                <td>
                    <span class="stat-value">{{ $inquiry->total_features_count }}</span>
                    <span class="stat-label">Total Features</span>
                </td>
                <td>
                    <span class="stat-value">{{ $inquiry->is_active ? '100%' : '0%' }}</span>
                    <span class="stat-label">Protection</span>
                </td>
            </tr>
        </table>

        <div class="section">
            <div class="section-title">Security Features</div>
            <table class="features-table">
                @php $index = 0; @endphp
                @foreach($features as $key => $value)
                    @if(isset($labels[$key]))
                        @if($index % 2 == 0)
                            <tr>
                            @endif
                            <td>
                                <div class="feature-row {{ $value ? 'active' : 'inactive' }}">
                                    <span class="feature-name">{{ $labels[$key] }}</span>
                                    <span class="feature-status">{{ $value ? '✓ ON' : '✗ OFF' }}</span>
                                </div>
                            </td>
                            @if($index % 2 == 1)
                            </tr>
                        @endif
                        @php $index++; @endphp
                    @endif
                @endforeach
                @if($index % 2 == 1)
                    <td></td></tr>
                @endif
            </table>
        </div>

        <div class="section">
            <div class="section-title">Compliance</div>
            <table class="info-table">
                <tr>
                    <td>GDPR</td>
                    <td>Compliant</td>
                </tr>
                <tr>
                    <td>ISO 27001</td>
                    <td>Certified</td>
                </tr>
                <tr>
                    <td>SSL/TLS</td>
                    <td>256-bit Encryption</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <strong>Booking System</strong> · support@booking.com · +1 (555) 123-4567<br>
            Report #SEC-{{ str_pad($inquiry->id, 4, '0', STR_PAD_LEFT) }} · Generated {{ now()->translatedFormat('M d, Y H:i') }}
        </div>

    </body>
</html>
