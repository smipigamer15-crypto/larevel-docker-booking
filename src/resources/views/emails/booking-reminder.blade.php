<!DOCTYPE html>
<html data-style-page="emails/booking-reminder">
    <head>
        <meta charset="utf-8">
        <title>Appointment Reminder</title>

        {!! '<style>' . file_get_contents(resource_path('css/emails.css')) . '</style>' !!}
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Appointment Reminder</h1>
                <p>Your visit is scheduled for tomorrow</p>
            </div>

            <div class="content">
                <div class="greeting">Hi {{ $booking->user->name }},</div>
                <p class="text">This is a friendly reminder about your upcoming appointment tomorrow. We look forward to seeing you!</p>

                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label">Service</span>
                        <span class="info-value">{{ $booking->service->title }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date</span>
                        <span class="info-value">{{ $booking->booking_date->format('l, F d, Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Time</span>
                        <span class="info-value time">{{ $booking->start_time }}</span>
                    </div>
                </div>

                <p class="text">If you need to reschedule or cancel, please do so as soon as possible.</p>

                <div class="btn-wrapper">
                    <a href="{{ route('bookings.show', $booking) }}" class="btn">
                        View Booking Details
                    </a>
                </div>
            </div>

            <div class="footer">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </body>
</html>
