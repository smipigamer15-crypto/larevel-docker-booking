<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Mail\BookingReminder;
use App\Services\MonitoringService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders';
    protected $description = 'Send reminder emails 24 hours before booking';

    public function handle(): int
    {
        $tomorrow = now()->addDay()->toDateString();

        $bookings = Booking::where('status', 'upcoming')
            ->whereDate('booking_date', $tomorrow)
            ->with(['user', 'service'])
            ->get();

        $sent = 0;

        foreach ($bookings as $booking) {
            if (!$booking->user || !$booking->user->email) {
                continue;
            }

            try {
                Mail::to($booking->user->email)->send(new BookingReminder($booking));
                $sent++;
                $this->info("Reminder → {$booking->user->email}");
            } catch (\Exception $e) {
                MonitoringService::error('email_failed', 'Booking reminder failed: ' . $e->getMessage(), ['booking_id' => $booking->id]);
                $this->error("Failed for #{$booking->id}: {$e->getMessage()}");
            }
        }

        $this->info("Total reminders sent: {$sent}");
        return Command::SUCCESS;
    }
}
