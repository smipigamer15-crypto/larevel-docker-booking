<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewBookingEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;
    public $message;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->message = "New booking created: {$booking->service->title} by {$booking->user->name}";
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
                'date' => $this->booking->booking_date->format('d.m.Y'),
                'time' => $this->booking->start_time,
                'status' => $this->booking->status,
            ],
            'url' => route('admin.bookings.show', $this->booking),
            'time' => now()->format('H:i'),
        ];
    }

    public function broadcastAs()
    {
        return 'new-booking';
    }
}
