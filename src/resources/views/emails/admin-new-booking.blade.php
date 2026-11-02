<!DOCTYPE html>
<html data-style-page="emails/admin-new-booking">
    <head>
        <meta charset="utf-8">
        <title>New Booking</title>

        {!! '<style>' . file_get_contents(resource_path('css/emails.css')) . '</style>' !!}
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>New Booking Received</h1>
                <p>A client just scheduled an appointment</p>
            </div>

            <div class="content">
                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label">Client Name</span>
                        <span class="info-value">{{ $booking->user->name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email Address</span>
                        <span class="info-value">{{ $booking->user->email }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone Number</span>
                        <span class="info-value">{{ $booking->user->phone ?? 'Not provided' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Service</span>
                        <span class="info-value">{{ $booking->service->title }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date & Time</span>
                        <span class="info-value">{{ $booking->booking_date->format('M d, Y') }} at {{ $booking->start_time }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total Price</span>
                        <span class="info-value price">${{ number_format($booking->service->price, 2) }}</span>
                    </div>
                </div>

                @if($booking->notes)
                    <div class="notes-box">
                        <strong data-view-style="emails-admin-new-booking-1">Client Notes:</strong>
                        {{ $booking->notes }}
                    </div>
                @endif

                <div class="btn-wrapper">
                    <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn">
                        View Booking in Admin
                    </a>
                </div>
            </div>

            <div class="footer">
                This is an automated notification from {{ config('app.name') }}.
            </div>
        </div>
    </body>
</html>
