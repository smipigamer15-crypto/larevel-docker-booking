<!DOCTYPE html>
<html data-style-page="emails/booking-completed">
    <head>
        <meta charset="utf-8">
        <title>Thank You</title>
        {!! '<style>' . file_get_contents(resource_path('css/emails.css')) . '</style>' !!}
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Thank You!</h1>
                <p>We hope you enjoyed your appointment</p>
            </div>

            <div class="content">
                <div class="greeting">Hi {{ $booking->user->name }},</div>
                <p class="text">Your appointment has been successfully completed. Thank you for choosing us!</p>

                <div class="info-box">
                    <h3 class="service-title">{{ $booking->service->title }}</h3>
                    <p class="service-meta">
                        {{ $booking->booking_date->format('l, F d, Y') }} at {{ $booking->start_time }}
                    </p>
                </div>

                <p class="text">We'd love to hear your feedback! Leave a review to help other clients and help us improve our service.</p>

                <div class="btn-wrapper">
                    <a href="{{ route('reviews.create', $booking->id) }}" class="btn">
                        Leave a Review
                    </a>
                </div>

                <p data-view-style="emails-booking-completed-1">
                    We can't wait to see you again — book your next appointment anytime.
                </p>
            </div>

            <div class="footer">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </body>
</html>
