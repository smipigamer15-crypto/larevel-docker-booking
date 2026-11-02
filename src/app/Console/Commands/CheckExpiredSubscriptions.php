<?php

namespace App\Console\Commands;

use App\Models\BookingSubscription;
use App\Models\SecurityInquiry;
use App\Models\User;
use Illuminate\Console\Command;

class CheckExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:check-expired';
    protected $description = 'Deactivate expired subscriptions for booking and security';

    public function handle()
    {
        $now = now();

        $expiredBookings = BookingSubscription::where('is_active', true)
            ->whereNotNull('subscription_ends_at')
            ->where('subscription_ends_at', '<', $now)
            ->get();

        foreach ($expiredBookings as $sub) {
            $sub->update([
                'is_active' => false,
                'status' => 'deactivated',
            ]);

            $user = User::where('email', $sub->email)->first();
            if ($user) {
                $user->update(['plan' => 'basic']);
            }

            $this->info("Booking subscription #{$sub->id} deactivated.");
        }

        $expiredSecurity = SecurityInquiry::where('is_active', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->get();

        foreach ($expiredSecurity as $inquiry) {
            $inquiry->update([
                'is_active' => false,
                'status' => 'deactivated',
            ]);
            $this->info("Security inquiry #{$inquiry->id} deactivated.");
        }

        $this->info('All expired subscriptions processed.');
    }
}
