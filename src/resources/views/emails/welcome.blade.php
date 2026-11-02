<!DOCTYPE html>
<html data-style-page="emails/welcome">
    <head>
        <meta charset="utf-8">
        <title>Welcome</title>

        {!! '<style>' . file_get_contents(resource_path('css/emails.css')) . '</style>' !!}
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Welcome Aboard</h1>
                <p>Your account is ready</p>
            </div>

            <div class="content">
                <div class="greeting">Hello {{ $user->name }},</div>
                <p class="text">Thank you for joining <strong>{{ config('app.name') }}</strong>. Your account is ready — you can start booking services right away.</p>

                <div class="info-box">
                    <div class="feature-item">
                        <div class="feature-dot"></div>
                        <div>Book any service 24/7</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-dot"></div>
                        <div>Get instant email confirmations</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-dot"></div>
                        <div>Friendly reminders before appointments</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-dot"></div>
                        <div>Leave reviews to help others</div>
                    </div>
                </div>

                <p class="text">Ready to get started? Make your first booking in just a few clicks.</p>

                <div class="btn-wrapper">
                    <a href="{{ route('bookings.create') }}" class="btn">Book Your First Appointment</a>
                </div>
            </div>

            <div class="footer">
                Need help? <a href="mailto:support@booking.com">support@booking.com</a><br>
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </body>
</html>
