<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public static function periods(): array
    {
        return [[7, 2], [30, 4], [90, 6]];
    }

    #[DataProvider('periods')]
    public function test_periods_filter_bookings_and_registrations_by_creation_date(int $days, int $expected): void
    {
        $this->travelTo(now()->setDate(2026, 11, 2)->setTime(12, 0));
        $this->withoutVite();
        $admin = User::factory()->create(['role' => 'admin', 'created_at' => now()->subYear()]);
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $service = Service::create([
            'category_id' => $category->id,
            'title' => 'Test Service',
            'description' => 'Test',
            'duration' => 30,
            'price' => 50,
            'is_active' => true,
        ]);

        foreach ([0, 6, 7, 29, 30, 89, 90] as $offset) {
            $createdAt = now()->subDays($offset)->startOfDay();
            $user = User::factory()->create(['created_at' => $createdAt]);
            $booking = new Booking([
                'user_id' => $user->id,
                'service_id' => $service->id,
                'booking_date' => now()->addYear()->toDateString(),
                'start_time' => '10:00:00',
                'end_time' => '10:30:00',
                'status' => 'upcoming',
            ]);
            $booking->created_at = $createdAt;
            $booking->save();
        }

        $response = $this->actingAs($admin)->get(route('admin.dashboard', ['days' => $days]));

        $response->assertOk()
            ->assertViewHas('totalBookings', $expected)
            ->assertViewHas('totalUsers', $expected)
            ->assertViewHas('selectedDays', $days)
            ->assertViewHas('chartData', function ($data) use ($days, $expected) {
                return count($data['labels']) === $days && array_sum($data['values']) === $expected && array_sum($data['registrations']) === $expected && $data['labels'][0] === now()->subDays($days - 1)->toDateString() && end($data['labels']) === now()->toDateString() && in_array(0, $data['values'], true);
            })
            ->assertSee('data-admin', false)
            ->assertDontSee('class="footer ', false)
            ->assertDontSee('class="navbar ', false);
    }

    public function test_invalid_periods_and_reversed_dates_are_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->getJson(route('admin.dashboard', ['days' => 999]))
            ->assertUnprocessable()->assertJsonValidationErrors('days');
        $this->getJson(route('admin.dashboard', ['start_date' => '2026-11-02', 'end_date' => '2026-11-01']))
            ->assertUnprocessable()->assertJsonValidationErrors('end_date');
    }

    public function test_custom_range_includes_both_dates_and_fills_empty_days(): void
    {
        $this->withoutVite();
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get(route('admin.dashboard', [
            'start_date' => '2020-01-01',
            'end_date' => '2020-01-03',
        ]))->assertOk()->assertViewHas('chartData', [
            'labels' => ['2020-01-01', '2020-01-02', '2020-01-03'],
            'values' => [0, 0, 0],
            'registrations' => [0, 0, 0],
        ]);
    }

    public function test_non_admin_users_cannot_access_the_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())->get(route('admin.dashboard'))
            ->assertRedirect(route('dashboard'));
    }
}
