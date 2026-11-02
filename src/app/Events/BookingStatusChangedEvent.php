<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingStatusChangedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;
    public $message;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->message = "Booking #{$booking->id} status changed to: " . ucfirst($booking->status);
    }

    public function broadcastOn()
    {
        return new Channel('admin-notifications');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->booking->id,
            'message' => $this->message,
            'booking' => [
                'service' => $this->booking->service->title,
                'user' => $this->booking->user->name,
                'status' => $this->booking->status,
            ],
            'url' => route('admin.bookings.show', $this->booking),
            'time' => now()->format('H:i'),
        ];
    }

    public function broadcastAs()
    {
        return 'booking-status-changed';
    }
}
