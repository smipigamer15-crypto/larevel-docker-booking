<!DOCTYPE html>
<html data-style-page="emails/review-request">
    <head>
        <meta charset="utf-8">
        <title>How was your visit?</title>

        {!! '<style>' . file_get_contents(resource_path('css/emails.css')) . '</style>' !!}
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>How was your visit?</h1>
                <p>We'd love to hear your feedback</p>
            </div>

            <div class="content">
                <div class="greeting">Hi {{ $booking->user->name }},</div>
                <p class="text">It's been a day since your visit. How was your experience?</p>

                <div class="info-box">
                    <h3 class="service-title">{{ $booking->service->title }}</h3>
                    <p class="service-meta">{{ $booking->booking_date->format('l, F d, Y') }}</p>
                </div>

                <p class="text">Your feedback helps other clients and helps us improve our service.</p>

                <div class="btn-wrapper">
                    <a href="{{ route('reviews.create', $booking->id) }}" class="btn">Write a Review</a>
                </div>

                <p data-view-style="emails-review-request-1">
                    Your review will take less than 1 minute.
                </p>
            </div>

            <div class="footer">
                © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </body>
</html>
