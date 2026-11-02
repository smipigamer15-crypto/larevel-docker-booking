<!DOCTYPE html>
<html data-style-page="emails/admin-security-notification">
    <head>
        <meta charset="utf-8">
        <title>New Security Inquiry</title>

        {!! '<style>' . file_get_contents(resource_path('css/emails.css')) . '</style>' !!}
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>New Security Inquiry</h1>
                <p>A new client requested a security package</p>
            </div>

            <div class="content">
                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label">Client Name</span>
                        <span class="info-value">{{ $inquiry->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email Address</span>
                        <span class="info-value">{{ $inquiry->email }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone Number</span>
                        <span class="info-value">{{ $inquiry->phone ?? 'Not provided' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Company</span>
                        <span class="info-value">{{ $inquiry->company ?? 'Not provided' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Requested Package</span>
                        <span class="info-value package">{{ $inquiry->package_label }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date Received</span>
                        <span class="info-value">{{ $inquiry->created_at->format('M d, Y \a\t H:i') }}</span>
                    </div>
                </div>

                @if($inquiry->message)
                    <div class="notes-box">
                        <strong data-view-style="emails-admin-security-notification-1">Client Message:</strong>
                        {{ $inquiry->message }}
                    </div>
                @endif

                <div class="btn-wrapper">
                    <a href="{{ route('admin.security.show', $inquiry->id) }}" class="btn">
                        View Inquiry in Admin
                    </a>
                </div>

                <div class="link-box">
                    <strong data-view-style="emails-admin-security-notification-2">Client Status Link:</strong><br>
                    <a href="{{ route('security.client.status', $inquiry->access_token) }}">
                        {{ route('security.client.status', $inquiry->access_token) }}
                    </a>
                </div>
            </div>

            <div class="footer">
                This is an automated notification from {{ config('app.name') }}.
            </div>
        </div>
    </body>
</html>
