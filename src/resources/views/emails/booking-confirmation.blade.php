<!DOCTYPE html>
<html data-style-page="emails/booking-confirmation">
    <head>
        <meta charset="utf-8">
        <title>Booking Confirmation</title>

        {!! '<style>' . file_get_contents(resource_path('css/emails.css')) . '</style>' !!}
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Booking Confirmed</h1>
                <p>Your appointment is scheduled</p>
            </div>

            <div class="content">
                <div class="greeting">Hello {{ $booking->user->name }},</div>
                <p class="text">Your booking has been successfully confirmed. We look forward to seeing you!</p>

                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label">Booking ID</span>
                        <span class="info-value">#{{ $booking->id }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Service</span>
                        <span class="info-value">{{ $booking->service->title }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date</span>
                        <span class="info-value">{{ $booking->booking_date->format('M d, Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Time</span>
                        <span class="info-value">{{ $booking->start_time }} — {{ $booking->end_time }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total Price</span>
                        <span class="info-value price">${{ number_format($booking->service->price, 2) }}</span>
                    </div>
                </div>

                <p class="text">If you need to reschedule or cancel, you can do so from your dashboard.</p>

                <div class="btn-wrapper">
                    <a href="{{ route('bookings.show', $booking) }}" class="btn">View Booking</a>
                </div>
            </div>

            <div class="footer">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </body>
</html>
