<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class CleanOldLogs extends Command
{
    protected $signature = 'monitoring:clean {--days=90}';
    protected $description = 'Clean old activity logs';

    public function handle()
    {
        $days = $this->option('days');
        $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();
        $this->info("Deleted {$deleted} logs older than {$days} days.");
    }
}
