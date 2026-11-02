<!DOCTYPE html>
<html data-style-page="subscription/booking-report">
    <head>
        <meta charset="utf-8">
        <title>Booking Subscription Report</title>

        {!! '<style>' . file_get_contents(resource_path('css/subscription.css')) . '</style>' !!}
    </head>
    <body>

        <div class="header">
            <h1>Booking Subscription Report</h1>
            <p>Generated on {{ now()->translatedFormat('M d, Y H:i') }}</p>
        </div>

        <div class="section">
            <h2>Subscription Details</h2>
            <table>
                <tr>
                    <td>Subscription ID</td>
                    <td>#{{ $subscription->id }}</td>
                </tr>
                <tr>
                    <td>Client Name</td>
                    <td>{{ $subscription->name }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{ $subscription->email }}</td>
                </tr>
                <tr>
                    <td>Plan</td>
                    <td><strong>{{ $subscription->plan_label }}</strong></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>
                        <span class="badge badge-{{ $subscription->is_active ? 'active' : $subscription->status }}">
                            {{ __(ucfirst($subscription->status ?? 'pending')) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>Started</td>
                    <td>{{ $subscription->access_activated_at?->translatedFormat('M d, Y') ?? $subscription->created_at->translatedFormat('M d, Y') }}</td>
                </tr>
                <tr>
                <td>@if($subscription->cancel_at_period_end) Ends on @else Next billing @endif</td>
                    <td>{{ $subscription->subscription_ends_at?->translatedFormat('M d, Y') ?? '—' }}</td>
                </tr>
                @if($subscription->subscription_ends_at && $subscription->is_active)
                    <tr>
                        <td>Days Remaining</td>
                        <td>{{ (int) ceil(now()->diffInDays($subscription->subscription_ends_at, false)) }} days</td>
                    </tr>
                @endif
            </table>
        </div>

        <div class="section">
            <h2>Included Features</h2>
            <div class="features">
                @php
                    $planFeatures = $subscription->package === 'pro'
                        ? ['Unlimited bookings', 'SMS notifications', '24/7 support', 'Additional integrations', 'Analytics & reports']
                        : ['All Pro features', 'API access', 'Multiple administrators', 'Priority support', 'White-label branding'];
                @endphp
                @foreach($planFeatures as $feature)
                    <div class="feature">✓ {{ $feature }}</div>
                @endforeach
            </div>
        </div>

        <div class="footer">
            This report was generated automatically by Booking System.<br>
            For questions, contact support@booking.com
        </div>

    </body>
</html>
