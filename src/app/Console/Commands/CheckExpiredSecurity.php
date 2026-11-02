<?php

namespace App\Console\Commands;

use App\Models\SecurityInquiry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckExpiredSecurity extends Command
{
    protected $signature = 'security:check-expired';
    protected $description = 'Check and deactivate expired security packages';

    public function handle()
    {
        $expired = SecurityInquiry::expired()->where('is_active', true)->get();

        foreach ($expired as $inquiry) {
            $inquiry->is_active = false;
            $inquiry->status = 'expired';
            $inquiry->save();

            Log::info('Security package expired for: ' . $inquiry->email);
            $this->info('Deactivated: ' . $inquiry->email);
        }

        $this->info('Checked ' . $expired->count() . ' expired packages');
    }
}
