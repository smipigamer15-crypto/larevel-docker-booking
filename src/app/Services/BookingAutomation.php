<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Support\Facades\Http;

class BookingAutomation
{
    public function send(int $userId, array $payload): void
    {
        $settings = app(FeatureWorkspace::class)->settings($userId);
        if (!$settings['zapier_enabled'] || !$settings['zapier_url']) {
            return;
        }
        if (!preg_match('~\Ahttps://hooks\.zapier\.com/hooks/catch/[A-Za-z0-9/_-]+/?\z~', $settings['zapier_url'])) {
            throw new \UnexpectedValueException('Invalid Zapier Catch Hook URL.');
        }
        $response = Http::withoutRedirecting()->timeout(5)->post($settings['zapier_url'], $payload);
        if (!$response->successful()) {
            throw new \RuntimeException('Zapier did not accept the booking event.');
        }
    }

    public function booking(int $bookingId, string $event): void
    {
        $booking = Booking::with('service')->find($bookingId);
        if (!$booking) {
            return;
        }
        $this->send($booking->user_id, [
            'event' => $event,
            'booking' => [
                'id' => $booking->id,
                'service' => $booking->service->title,
                'date' => $booking->booking_date->toDateString(),
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'status' => $booking->status,
            ],
        ]);
    }
}
