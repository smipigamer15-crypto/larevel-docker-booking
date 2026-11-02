<?php

namespace Tests\Feature;

use App\Jobs\SendBookingWebhook;
use App\Mail\BookingConfirmation;
use App\Models\BlockedDate;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;

    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Carbon::setTestNow(Carbon::parse('2026-11-02 12:00:00'));
        Mail::fake();
        Bus::fake([SendBookingWebhook::class]);
        Http::preventStrayRequests();
        $this->customer = User::factory()->create();
        $category = Category::create(['name' => 'Wellness', 'slug' => 'wellness']);
        $this->service = Service::create([
            'category_id' => $category->id, 'title' => 'Consultation', 'description' => 'A consultation',
            'price' => 50, 'duration' => 30, 'is_active' => true,
        ]);
        $this->actingAs($this->customer);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function payload(array $changes = []): array
    {
        return array_replace([
            'service_id' => $this->service->id, 'booking_date' => '2026-11-03',
            'start_time' => '10:00', 'end_time' => '10:30', 'notes' => 'Release test',
        ], $changes);
    }

    private function existing(array $changes = []): Booking
    {
        return Booking::create(array_replace($this->payload(), [
            'user_id' => $this->customer->id, 'status' => 'upcoming',
        ], $changes));
    }

    public function test_customer_pages_render_in_all_five_languages(): void
    {
        $booking = $this->existing();
        foreach (['en', 'uk', 'ru', 'pl', 'de'] as $locale) {
            $this->withSession(['locale' => $locale]);
            foreach (['/dashboard', '/profile', '/bookings', '/bookings/create', '/bookings/'.$booking->id, '/booking/checkout/pro'] as $url) {
                $this->get($url)->assertOk()->assertHeader('Content-Language', $locale);
            }
        }
    }

    public function test_web_booking_can_be_created_cancelled_and_booked_again(): void
    {
        $this->post('/bookings', $this->payload())->assertRedirect(route('bookings.index'));
        $booking = Booking::firstOrFail();
        $this->assertSame($this->customer->id, $booking->user_id);
        Mail::assertSent(BookingConfirmation::class);
        $this->patch('/bookings/'.$booking->id.'/cancel')->assertSessionHas('success');
        $this->assertSame('cancelled', $booking->fresh()->status);
        $this->post('/bookings', $this->payload())->assertSessionHasNoErrors();
        $this->assertSame(1, Booking::where('status', 'upcoming')->count());
    }

    public function test_adjacent_appointments_are_allowed_in_the_web_and_api(): void
    {
        $this->existing(['start_time' => '09:30', 'end_time' => '10:00']);
        $this->post('/bookings', $this->payload())->assertSessionHasNoErrors();
        Sanctum::actingAs($this->customer, ['bookings:write']);
        $this->postJson('/api/v1/bookings', $this->payload(['start_time' => '10:30', 'end_time' => '11:00']))
            ->assertCreated()->assertJsonPath('success', true);
        $this->assertDatabaseCount('bookings', 3);
    }

    public function test_an_appointment_fully_inside_an_existing_booking_is_rejected(): void
    {
        $this->existing(['start_time' => '09:00', 'end_time' => '11:00']);
        $this->post('/bookings', $this->payload())->assertSessionHasErrors('start_time');
        Sanctum::actingAs($this->customer, ['bookings:write']);
        $this->postJson('/api/v1/bookings', $this->payload())->assertConflict();
        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_a_customer_cannot_overlap_their_own_booking_for_another_service(): void
    {
        $other = $this->service->replicate();
        $other->title = 'Another consultation';
        $other->save();
        $this->existing(['service_id' => $other->id]);
        $this->getJson('/booking/slots?service_id='.$this->service->id.'&date=2026-11-03')
            ->assertOk()->assertJsonMissing(['start' => '10:00', 'end' => '10:30']);
        Sanctum::actingAs($this->customer, ['bookings:write']);
        $this->postJson('/api/v1/bookings', $this->payload())->assertConflict();
    }

    public function test_blocked_days_are_rejected_in_the_web_and_api(): void
    {
        BlockedDate::create(['date' => '2026-11-03', 'reason' => 'Closed']);
        $this->getJson('/booking/slots?service_id='.$this->service->id.'&date=2026-11-03')->assertJsonPath('slots', []);
        $this->post('/bookings', $this->payload())->assertSessionHasErrors('booking_date');
        Sanctum::actingAs($this->customer, ['bookings:write']);
        $this->postJson('/api/v1/bookings', $this->payload())->assertUnprocessable();
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_end_times_and_service_duration_cannot_be_tampered_with(): void
    {
        $this->post('/bookings', $this->payload(['end_time' => '10:01']))->assertSessionHasErrors('end_time');
        $this->post('/bookings', $this->payload(['start_time' => '20:45', 'end_time' => '21:15']))->assertSessionHasErrors('start_time');
        $this->post('/bookings', $this->payload(['notes' => ['invalid']]))->assertSessionHasErrors('notes');
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_inactive_services_and_past_times_cannot_be_booked(): void
    {
        $this->post('/bookings', $this->payload(['booking_date' => '2026-11-02', 'start_time' => '11:00', 'end_time' => '11:30']))
            ->assertSessionHasErrors('start_time');
        $this->getJson('/booking/slots?service_id='.$this->service->id.'&date=2026-11-02')
            ->assertJsonMissing(['start' => '09:00', 'end' => '09:30']);
        $this->service->update(['is_active' => false]);
        $this->post('/bookings', $this->payload())->assertSessionHasErrors('service_id');
        $this->getJson('/booking/slots?service_id='.$this->service->id.'&date=2026-11-03')->assertUnprocessable();
    }

    public function test_api_cannot_bypass_the_monthly_booking_limit(): void
    {
        foreach (range(1, 10) as $index) {
            $this->existing(['booking_date' => '2026-12-01', 'status' => 'cancelled']);
        }
        Sanctum::actingAs($this->customer, ['bookings:write']);
        $this->postJson('/api/v1/bookings', $this->payload())->assertUnprocessable()->assertJsonValidationErrors('service_id');
        $this->assertDatabaseCount('bookings', 10);
    }

    public function test_calendar_handles_empty_days_booked_days_and_blocked_days(): void
    {
        $this->existing();
        BlockedDate::create(['date' => '2026-11-04']);
        $this->getJson('/booking/calendar?year=2026&month=11')->assertOk()
            ->assertJsonPath('2026-11-01.bookings_count', 0)
            ->assertJsonPath('2026-11-03.bookings_count', 1)
            ->assertJsonPath('2026-11-04.is_blocked', true);
        $this->getJson('/booking/calendar?year=2026&month=13')->assertUnprocessable();
    }

    public function test_other_customers_cannot_read_or_cancel_another_customers_booking(): void
    {
        $booking = $this->existing();
        $other = User::factory()->create();
        $this->actingAs($other)->get('/bookings/'.$booking->id)->assertForbidden();
        $this->patch('/bookings/'.$booking->id.'/cancel')->assertForbidden();
        Sanctum::actingAs($other, ['bookings:write']);
        $this->patchJson('/api/v1/bookings/'.$booking->id.'/cancel')->assertNotFound();
        $this->assertSame('upcoming', $booking->fresh()->status);
    }

    public function test_past_dates_are_grouped_as_completed_and_invalid_api_filters_are_rejected(): void
    {
        $past = $this->existing(['booking_date' => '2026-11-01']);
        $future = $this->existing();
        $this->get('/bookings')->assertOk()
            ->assertViewHas('completed', fn ($bookings) => $bookings->contains('id', $past->id))
            ->assertViewHas('upcoming', fn ($bookings) => $bookings->count() === 1 && $bookings->contains('id', $future->id));
        Sanctum::actingAs($this->customer, ['bookings:read', 'services:read']);
        $this->getJson('/api/v1/bookings?per_page=0')->assertUnprocessable()->assertJsonValidationErrors('per_page');
        $this->getJson('/api/v1/bookings?status[]=upcoming')->assertUnprocessable()->assertJsonValidationErrors('status');
        $this->getJson('/api/v1/services?per_page=10000')->assertUnprocessable()->assertJsonValidationErrors('per_page');
        $this->getJson('/api/v1/services?search[]=test')->assertUnprocessable()->assertJsonValidationErrors('search');
    }
}
