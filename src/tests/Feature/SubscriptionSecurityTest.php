<?php

namespace Tests\Feature;

use App\Models\BookingSubscription;
use App\Models\SecurityInquiry;
use App\Models\User;
use App\Services\StripeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SubscriptionSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function subscription(User $owner, array $attributes = []): BookingSubscription
    {
        return BookingSubscription::create(array_merge([
            'name' => $owner->name,
            'email' => $owner->email,
            'package' => 'pro',
            'status' => 'approved',
            'is_active' => true,
            'access_token' => Str::random(32),
            'expires_at' => now()->addMonth(),
        ], $attributes));
    }

    public function test_guests_cannot_open_a_profile(): void
    {
        $this->get('/profile')->assertRedirect('/login');
    }

    public function test_unsigned_webhooks_are_rejected(): void
    {
        $this->mock(StripeService::class)->shouldNotReceive('handleWebhook');
        $this->postJson('/stripe/webhook', [])->assertStatus(400);
    }

    public function test_a_missing_webhook_secret_is_rejected_without_a_server_error(): void
    {
        config(['services.stripe.webhook_secret' => null]);
        $this->withHeader('Stripe-Signature', 't=1,v1=invalid')->postJson('/stripe/webhook', [])->assertStatus(400);
    }

    public function test_an_unpaid_checkout_webhook_does_not_activate_a_subscription(): void
    {
        $user = User::factory()->create();
        $subscription = $this->subscription($user, ['is_active' => false, 'status' => 'pending']);
        $this->mock(StripeService::class)->shouldReceive('handleWebhook')->once()->andReturn((object) [
            'type' => 'checkout.session.completed',
            'data' => (object) [
                'object' => (object) [
                    'mode' => 'subscription',
                    'payment_status' => 'unpaid',
                    'subscription' => 'sub_test',
                    'metadata' => (object) ['booking_subscription_id' => $subscription->id],
                ],
            ],
        ]);

        $this->withHeader('Stripe-Signature', 'test-signature')->postJson('/stripe/webhook', [])->assertOk();
        $this->assertFalse($subscription->fresh()->is_active);
    }

    public function test_a_public_activation_link_cannot_activate_an_unpaid_package(): void
    {
        $inquiry = SecurityInquiry::create([
            'name' => 'Pending Customer',
            'email' => 'pending@example.com',
            'package' => 'pro',
            'status' => 'pending',
            'is_active' => false,
            'access_token' => Str::random(32),
        ]);

        $this->get(route('security.client.activate', ['token' => $inquiry->access_token, 'duration' => '1_year']))
            ->assertForbidden();

        $this->assertFalse($inquiry->fresh()->is_active);
        $this->assertSame('pending', $inquiry->fresh()->status);
    }

    public function test_an_active_package_cannot_be_upgraded_by_starting_checkout(): void
    {
        $user = User::factory()->create();
        $subscription = $this->subscription($user);
        config(['services.stripe.prices.booking_enterprise' => 'price_test']);
        $this->mock(StripeService::class)->shouldNotReceive('createBookingCheckoutSession');

        $this->actingAs($user)->post(route('booking.subscription.checkout', 'business'), [
            'name' => $user->name,
            'email' => $user->email,
        ])->assertSessionHas('error');

        $this->assertSame('pro', $subscription->fresh()->package);
    }

    public function test_unverified_users_cannot_claim_a_subscription_by_email(): void
    {
        $user = User::factory()->unverified()->create();
        $subscription = $this->subscription($user);

        $this->assertFalse($user->hasActiveBookingSubscription());
        $this->actingAs($user)->get(route('booking.subscription.manage', $subscription->access_token))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_users_cannot_manage_or_verify_someone_elses_subscription(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $subscription = $this->subscription($owner);
        $this->mock(StripeService::class)->shouldNotReceive('retrievePaidCheckoutSession');

        $this->actingAs($other);
        foreach (['manage', 'report'] as $action) {
            $this->get(route('booking.subscription.'.$action, $subscription->access_token))->assertForbidden();
        }
        foreach (['cancel.subscription', 'resume'] as $action) {
            $this->post(route('booking.subscription.'.$action, $subscription->access_token))->assertForbidden();
        }
        $this->get(route('booking.subscription.success', ['subscription' => $subscription->id, 'session_id' => 'cs_other']))
            ->assertForbidden();
    }

    public function test_checkout_cannot_modify_someone_elses_subscription(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $subscription = $this->subscription($owner);
        $this->mock(StripeService::class)->shouldNotReceive('createBookingCheckoutSession');

        $this->actingAs($other)->post(route('booking.subscription.checkout', 'business'), [
            'name' => $owner->name,
            'email' => $owner->email,
        ])->assertForbidden();

        $this->assertSame('pro', $subscription->fresh()->package);
    }

    public function test_inactive_or_expired_subscriptions_do_not_grant_access(): void
    {
        $user = User::factory()->create();
        $subscription = $this->subscription($user);
        $this->assertTrue($user->hasActiveBookingSubscription());
        $subscription->update(['is_active' => false]);
        $this->assertFalse($user->hasActiveBookingSubscription());
        $subscription->update(['is_active' => true, 'expires_at' => now()->subMinute()]);
        $this->assertFalse($user->hasActiveBookingSubscription());
    }
}
