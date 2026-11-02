<!DOCTYPE html>
<html data-style-page="emails/subscription-expiring-soon">
    <head>
        <meta charset="utf-8">
        <title>Subscription Expiring</title>

        {!! '<style>' . file_get_contents(resource_path('css/emails.css')) . '</style>' !!}
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Subscription Ending Soon</h1>
            </div>

            <div class="content">
                <div class="greeting">Hello {{ $subscription->name }},</div>

                <p class="text">
                    This is a friendly reminder that your <strong>{{ $subscription->plan_label }}</strong> plan will expire on
                    <strong>{{ $subscription->subscription_ends_at->format('M d, Y') }}</strong>.
                </p>

                <p class="text">
                    To continue enjoying unlimited bookings and premium features, please ensure your payment method is up to date.
                </p>

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
