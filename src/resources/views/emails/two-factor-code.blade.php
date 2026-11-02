<!DOCTYPE html>
<html data-style-page="emails/two-factor-code">
    <head>
        <meta charset="utf-8">
        <title>Two-Factor Authentication Code</title>

        {!! '<style>' . file_get_contents(resource_path('css/emails.css')) . '</style>' !!}
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Verification Code</h1>
                <p>Two-Factor Authentication</p>
            </div>

            <div class="content">
                <div class="greeting">Hello, {{ $user->name }}</div>
                <p class="text">Please use the following code to complete your login:</p>

                <div class="code-box">
                    <div class="code">{{ $code }}</div>
                </div>

                <p class="expires">This code will expire in 1 minute.</p>

                <div class="warning-box">
                    <strong data-view-style="emails-two-factor-code-1">Security Notice</strong>
                    If you didn't request this code, please ignore this email and consider changing your password immediately.
                </div>
            </div>

            <div class="footer">
                {{ config('app.name') }} · <a href="mailto:support@booking.com">support@booking.com</a><br>
                This is an automated message, please do not reply.
            </div>
        </div>
    </body>
</html>
