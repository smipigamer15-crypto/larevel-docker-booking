<?php

namespace App\Console\Commands;

use App\Models\SecurityInquiry;
use App\Models\BookingSubscription;
use Illuminate\Console\Command;

class DeactivateExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:deactivate-expired';
    protected $description = 'Deactivate all expired security and booking subscriptions';

    public function handle()
    {
        $now = now();

        $securityExpired = SecurityInquiry::where('is_active', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->get();

        foreach ($securityExpired as $inquiry) {
            $inquiry->update([
                'is_active' => false,
                'status' => 'deactivated',
            ]);
            $this->info("Security #{$inquiry->id} deactivated.");
        }

        $bookingExpired = BookingSubscription::where('is_active', true)
            ->whereNotNull('subscription_ends_at')
            ->where('subscription_ends_at', '<', $now)
            ->get();

        foreach ($bookingExpired as $sub) {
            $sub->update([
                'is_active' => false,
                'status' => 'deactivated',
            ]);

            $user = \App\Models\User::where('email', $sub->email)->first();
            if ($user) {
                $user->update(['plan' => 'basic']);
            }

            $this->info("Booking #{$sub->id} deactivated.");
        }

        $this->info("Done processing expired subscriptions.");
    }
}
