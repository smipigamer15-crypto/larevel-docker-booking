<?php

namespace App\Jobs;

use App\Services\BookingAutomation;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendBookingWebhook
{
    use Dispatchable;
    
    public function __construct(public int $bookingId, public string $event)
    {
    }
    
    public function handle(BookingAutomation $automation): void
    {
        try {
            $automation->booking($this->bookingId, $this->event);
        } catch (\Throwable $exception) {
            Log::warning('Booking webhook delivery failed.', ['booking_id' => $this->bookingId]);
        }
    }
}
