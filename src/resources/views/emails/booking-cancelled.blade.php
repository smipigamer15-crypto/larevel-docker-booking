<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Booking Cancelled</title>

    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Booking Cancelled</h1>
                <p>Your appointment has been cancelled</p>
            </div>

            <div class="content">
                <div class="greeting">Hello {{ $booking->user->name }},</div>
                <p class="text">Your booking has been successfully cancelled. Below are the details of the cancelled appointment.</p>

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
                        <span class="info-label">Status</span>
                        <span class="info-value danger">Cancelled</span>
                    </div>
                </div>

                <p class="text">If this was a mistake, you can easily book a new appointment at any time.</p>

                <div class="btn-wrapper">
                    <a href="{{ route('bookings.create') }}" class="btn">
                        Book Again
                    </a>
                </div>
            </div>

            <div class="footer">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </body>
</html>
