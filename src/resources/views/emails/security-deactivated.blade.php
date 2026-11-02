<!DOCTYPE html>
<html data-style-page="emails/security-deactivated">
    <head>
        <meta charset="utf-8">
        <title>Security Package Deactivated</title>

        {!! '<style>' . file_get_contents(resource_path('css/emails.css')) . '</style>' !!}
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Package Deactivated</h1>
                <p>Your security package is no longer active</p>
            </div>

            <div class="content">
                <div class="greeting">Hello {{ $inquiry->name }},</div>
                <p class="text">Your security package has been deactivated. Your site is no longer protected by our enterprise-grade security features.</p>

                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label">Package</span>
                        <span class="info-value">{{ $inquiry->package_label }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="info-value danger">Deactivated</span>
                    </div>
                </div>

                <p class="text">If you have any questions or would like to reactivate your subscription, please contact our support team.</p>

                <div class="btn-wrapper">
                    <a href="mailto:support@booking.com" class="btn">
                        Contact Support
                    </a>
                </div>
            </div>

            <div class="footer">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </body>
</html>
