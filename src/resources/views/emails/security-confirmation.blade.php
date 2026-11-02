<!DOCTYPE html>
<html data-style-page="emails/security-confirmation">
    <head>
        <meta charset="utf-8">
        <title>Security Package Request</title>

        {!! '<style>' . file_get_contents(resource_path('css/emails.css')) . '</style>' !!}
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Request Received</h1>
                <p>We've received your security package inquiry</p>
            </div>

            <div class="content">
                <div class="greeting">Hello {{ $inquiry->name }},</div>
                <p class="text">Thank you for your interest in our security packages. We have received your request and will contact you shortly.</p>

                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label">Selected Package</span>
                        <span class="info-value package">{{ $inquiry->package_label }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Current Status</span>
                        <span class="info-value status">{{ $inquiry->status_label }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Request Date</span>
                        <span class="info-value">{{ $inquiry->created_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>

                <div class="link-box">
                    <strong data-view-style="emails-security-confirmation-1">Your Personal Status Link:</strong>
                    <a href="{{ route('security.client.status', $inquiry->access_token) }}" target="_blank">
                        {{ route('security.client.status', $inquiry->access_token) }}
                    </a>
                    <div data-view-style="emails-security-confirmation-2">Save this link to check your status anytime.</div>
                </div>

                <div class="steps">
                    <h3>What happens next?</h3>
                    <ol>
                        <li>Our security team will review your request</li>
                        <li>You will receive a notification when your request is approved</li>
                        <li>Once approved, you can activate your security package</li>
                    </ol>
                </div>

                <div class="btn-wrapper">
                    <a href="{{ route('security.client.status', $inquiry->access_token) }}" class="btn">Check Status</a>
                </div>
            </div>

            <div class="footer">
                Need help? <a href="mailto:support@booking.com" data-view-style="emails-security-confirmation-3">support@booking.com</a><br>
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </body>
</html>
