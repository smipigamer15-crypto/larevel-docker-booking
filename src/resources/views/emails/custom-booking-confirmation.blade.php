<!DOCTYPE html>
<html data-style-page="emails/booking-confirmation"><head><meta charset="utf-8"><title>Booking confirmation</title>
        {!! '<style>' . file_get_contents(resource_path('css/emails.css')) . '</style>' !!}
</head><body><div class="container"><div class="header"><h1>Booking confirmed</h1></div><div class="content"><p class="text">{!! nl2br(e($customMessage)) !!}</p><p><a href="{{ route('bookings.show', $booking) }}">View your booking</a></p></div></div></body></html>
