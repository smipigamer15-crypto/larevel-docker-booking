<!DOCTYPE html>
<html data-style-page="emails/booking-subscription-renewed">
    <head>
        <meta charset="utf-8">
        <title>Subscription Renewed</title>

        {!! '<style>' . file_get_contents(resource_path('css/emails.css')) . '</style>' !!}
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Subscription Renewed</h1>
            </div>

            <div class="content">
                <div class="greeting">Hello {{ $subscription->name }},</div>

                <p class="text">
                    Your <strong>{{ $subscription->plan_label }}</strong> subscription has been automatically renewed. Your payment was processed successfully.
                </p>

                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label">Plan</span>
                        <span class="info-value">{{ $subscription->plan_label }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Amount Charged</span>
                        <span class="info-value price">${{ number_format($amount, 2) }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Next Billing</span>
                        <span class="info-value">{{ $subscription->subscription_ends_at?->format('M d, Y') ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="info-value" data-view-style="emails-booking-subscription-renewed-1">Active</span>
                    </div>
                </div>

                <div class="btn-wrapper">
                    <a href="{{ route('booking.subscription.manage', $subscription->access_token) }}" class="btn">
                        Manage Subscription
                    </a>
                </div>
            </div>

            <div class="footer">
                © {{ date('Y') }} {{ config('app.name') }}
            </div>
        </div>
    </body>
</html>
