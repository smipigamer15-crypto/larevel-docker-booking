<!DOCTYPE html>
<html data-style-page="emails/booking-subscription-activated">
    <head>
        <meta charset="utf-8">
        <title>Subscription Activated</title>

        {!! '<style>' . file_get_contents(resource_path('css/emails.css')) . '</style>' !!}
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Subscription Activated</h1>
                <p>Your plan is now live</p>
            </div>

            <div class="content">
                <div class="greeting">Hello {{ $subscription->name }},</div>
                <p class="text">Your subscription has been successfully activated. You now have full access to all features of your plan.</p>

                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label">Plan</span>
                        <span class="info-value plan">{{ $subscription->plan_label }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Billing Amount</span>
                        <span class="info-value">${{ $subscription->package === 'pro' ? '29' : '99' }} / month</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Next Billing</span>
                        <span class="info-value">{{ $subscription->subscription_ends_at?->format('M d, Y') ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="info-value active">Active</span>
                    </div>
                </div>

                <p class="text">You can manage your subscription (view details, change plan, or cancel) using the link below.</p>

                <div class="btn-wrapper">
                    <a href="{{ route('booking.subscription.manage', $subscription->access_token) }}" class="btn">
                        Manage Subscription
                    </a>
                </div>
            </div>

            <div class="footer">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </body>
</html>
