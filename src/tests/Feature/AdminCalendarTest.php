<?php

namespace Tests\Feature;

use App\Models\BlockedDate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_block_update_and_unblock_a_day(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->get(route('admin.calendar'))->assertOk();
        $date = now()->addDay()->toDateString();
        $this->post(route('admin.calendar.block'), ['date' => $date, 'reason' => 'Closed'])
            ->assertRedirect(route('admin.calendar'));
        $this->post(route('admin.calendar.block'), ['date' => $date, 'reason' => 'Holiday'])
            ->assertSessionHasNoErrors();
        $this->assertSame(1, BlockedDate::count());
        $this->get(route('admin.calendar'))->assertOk()->assertSee('Holiday');
        $this->delete(route('admin.calendar.unblock'), ['id' => BlockedDate::sole()->id])
            ->assertRedirect(route('admin.calendar'));
        $this->assertDatabaseCount('blocked_dates', 0);
    }

    public function test_calendar_requires_admin_access_and_valid_dates(): void
    {
        $this->actingAs(User::factory()->create())->post(route('admin.calendar.block'), ['date' => now()->toDateString()])
            ->assertRedirect(route('dashboard'));
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->postJson(route('admin.calendar.block'), ['date' => 'invalid'])->assertUnprocessable();
        $this->deleteJson(route('admin.calendar.unblock'), ['id' => 999])->assertUnprocessable();
        $this->assertDatabaseCount('blocked_dates', 0);
    }
}
