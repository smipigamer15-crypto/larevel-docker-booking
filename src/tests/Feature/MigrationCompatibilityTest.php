<?php

namespace Tests\Feature;

use App\Models\BlockedDate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigrationCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_blocked_date_migration_can_be_repeated_and_rolled_back_without_losing_dates(): void
    {
        $blockedDate = BlockedDate::create(['date' => now()->addDay()->toDateString(), 'reason' => 'Holiday']);
        $migration = require database_path('migrations/2026_09_27_110600_create_blocked_dates_table.php');
        $migration->up();
        $this->assertModelExists($blockedDate);
        $migration->down();
        $this->assertSame('Holiday', $blockedDate->fresh()->reason);
        $this->assertFalse(Schema::hasColumn('blocked_dates', 'start_time'));
        $migration->up();
        $this->assertTrue(Schema::hasColumn('blocked_dates', 'start_time'));
        $this->assertTrue(Schema::hasColumn('blocked_dates', 'created_by'));
        $this->assertModelExists($blockedDate);
    }
}
