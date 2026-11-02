<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Service;
use App\Services\BookingCalendar;
use Tests\TestCase;

class BookingCalendarTest extends TestCase
{
    public function test_calendar_escapes_untrusted_text_and_folds_utf8_lines(): void
    {
        $booking = (new Booking())->forceFill([
            'id' => 5,
            'booking_date' => '2026-11-03',
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => 'cancelled',
            'updated_at' => now(),
        ]);
        $booking->setRelation('service', new Service(['title' => "Consultation, long; title\n" . str_repeat('ї', 60)]));
        $ics = app(BookingCalendar::class)->render(collect([$booking]));
        $this->assertStringContainsString('STATUS:CANCELLED', $ics);
        $this->assertStringContainsString('SUMMARY:Consultation\, long\; title\n', $ics);
        foreach (explode("\r\n", $ics) as $line) {
            $this->assertLessThanOrEqual(75, strlen($line));
            $this->assertTrue(mb_check_encoding($line, 'UTF-8'));
        }
        $this->assertStringContainsString('DTSTART:', $ics);
        $this->assertStringNotContainsString('EMAIL', $ics);
    }
}
