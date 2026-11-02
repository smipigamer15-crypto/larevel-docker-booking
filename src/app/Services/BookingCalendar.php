<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class BookingCalendar
{
    public function render(Collection $bookings): string
    {
        $escape = fn($value) => str_replace(["\\", "\r", "\n", ',', ';'], ["\\\\", '', '\n', '\,', '\;'], $value);
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Booking//Appointments//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:My bookings',
        ];
        foreach ($bookings as $booking) {
            $date = $booking->booking_date->toDateString();
            $start = Carbon::parse($date . ' ' . $booking->start_time, config('app.timezone'))->utc();
            $end = Carbon::parse($date . ' ' . $booking->end_time, config('app.timezone'))->utc();
            array_push($lines, 'BEGIN:VEVENT', 'UID:booking-' . $booking->id . '@booking', 'DTSTAMP:' . $booking->updated_at->copy()->utc()->format('Ymd\THis\Z'), 'DTSTART:' . $start->format('Ymd\THis\Z'), 'DTEND:' . $end->format('Ymd\THis\Z'), 'SUMMARY:' . $escape($booking->service->title), 'STATUS:' . ($booking->status === 'cancelled' ? 'CANCELLED' : 'CONFIRMED'), 'END:VEVENT');
        }
        $lines[] = 'END:VCALENDAR';
        $folded = [];
        foreach ($lines as $line) {
            while (strlen($line) > 75) {
                $part = mb_strcut($line, 0, 75, 'UTF-8');
                $folded[] = $part;
                $line = ' ' . substr($line, strlen($part));
            }
            $folded[] = $line;
        }

        return implode("\r\n", $folded) . "\r\n";
    }
}
