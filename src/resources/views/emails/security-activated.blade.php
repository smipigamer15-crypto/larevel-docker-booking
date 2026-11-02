<!DOCTYPE html>
<html data-style-page="emails/security-activated">
    <head>
        <meta charset="utf-8">
        <title>Security Package Activated</title>

        {!! '<style>' . file_get_contents(resource_path('css/emails.css')) . '</style>' !!}
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Package Activated</h1>
                <p>Your security package is now active</p>
            </div>

            <div class="content">
                <div class="greeting">Hello {{ $inquiry->name }},</div>
                <p class="text">Good news! Your security package has been successfully activated. Your business is now protected.</p>

                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label">Package</span>
                        <span class="info-value package">{{ $inquiry->package_label }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Duration</span>
                        <span class="info-value">{{ $inquiry->duration_label }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Activated</span>
                        <span class="info-value">{{ $inquiry->access_activated_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Expires</span>
                        <span class="info-value">{{ $inquiry->expires_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="info-value active">Active</span>
                    </div>
                </div>

                <div class="features-box">
                    <h3>What's included in your package:</h3>
                    <ul>
                        <li>SSL Encryption</li>
                        <li>2FA Authentication</li>
                        <li>24/7 Security Monitoring</li>
                        <li>Daily Backups</li>
                    </ul>
                </div>

                <p class="text">You can check your security status anytime using the link below.</p>

                <div class="btn-wrapper">
                    <a href="{{ route('security.client.status', $inquiry->access_token) }}" class="btn">View Security Status</a>
                </div>
            </div>

            <div class="footer">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </body>
</html>
