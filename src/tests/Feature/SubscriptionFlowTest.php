<?php

namespace Tests\Feature;

use App\Mail\BookingSubscriptionActivated;
use App\Mail\SecurityAccessActivated;
use App\Models\BookingSubscription;
use App\Models\SecurityInquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Stripe\ApiRequestor;
use Stripe\HttpClient\ClientInterface;
use Tests\TestCase;

class SubscriptionFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private array $requests = [];

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.stripe.secret' => 'sk_test_fixture', 'services.stripe.prices.booking_pro' => 'price_fixture']);
        $this->owner = User::factory()->create();
        $this->actingAs($this->owner);
    }

    protected function tearDown(): void
    {
        ApiRequestor::setHttpClient(null);
        parent::tearDown();
    }

    private function subscription(array $attributes = []): BookingSubscription
    {
        return BookingSubscription::create(array_merge([
            'name' => $this->owner->name,
            'email' => $this->owner->email,
            'package' => 'pro',
            'status' => 'pending',
            'is_active' => false,
            'access_token' => Str::random(32),
            'stripe_customer_id' => 'cus_fixture',
        ], $attributes));
    }

    private function stripe(callable $respond): void
    {
        $client = $this->createStub(ClientInterface::class);
        $client->method('request')->willReturnCallback(function ($method, $url, $headers, $params) use ($respond) {
            $this->requests[] = [$method, parse_url($url, PHP_URL_PATH), $params];

            return [json_encode($respond($method, parse_url($url, PHP_URL_PATH), $params), JSON_THROW_ON_ERROR), 200, []];
        });
        ApiRequestor::setHttpClient($client);
    }

    private function paidSession(BookingSubscription $subscription, string $paymentStatus = 'paid'): array
    {
        return [
            'id' => 'cs_fixture', 'object' => 'checkout.session',
            'mode' => 'subscription', 'status' => 'complete', 'payment_status' => $paymentStatus,
            'customer' => 'cus_fixture',
            'metadata' => ['booking_subscription_id' => (string) $subscription->id, 'package' => 'pro'],
            'subscription' => [
                'id' => 'sub_fixture', 'object' => 'subscription', 'customer' => 'cus_fixture',
                'status' => 'active', 'cancel_at_period_end' => false,
                'current_period_end' => now()->addMonth()->timestamp,
            ],
        ];
    }

    public function test_checkout_creates_a_pending_subscription_and_redirects_to_stripe(): void
    {
        $this->stripe(fn ($method, $path) => $path === '/v1/customers'
            ? ['id' => 'cus_fixture', 'object' => 'customer']
            : ['id' => 'cs_fixture', 'object' => 'checkout.session', 'url' => 'https://checkout.stripe.com/c/pay/fixture']);

        $this->post(route('booking.subscription.checkout', 'pro'), [
            'name' => $this->owner->name, 'email' => $this->owner->email,
        ])->assertRedirect('https://checkout.stripe.com/c/pay/fixture');

        $subscription = BookingSubscription::sole();
        $this->assertFalse($subscription->is_active);
        $this->assertSame('pending', $subscription->status);
        $params = $this->requests[1][2];
        $this->assertSame('subscription', $params['mode']);
        $this->assertSame('price_fixture', $params['line_items'][0]['price']);
        $this->assertStringContainsString('{CHECKOUT_SESSION_ID}', $params['success_url']);
        $this->assertEquals($subscription->id, $params['subscription_data']['metadata']['booking_subscription_id']);
    }

    public function test_paid_checkout_activates_once_and_records_the_billing_period(): void
    {
        $subscription = $this->subscription();
        $this->stripe(fn () => $this->paidSession($subscription));
        $url = route('booking.subscription.success', ['subscription' => $subscription->id, 'session_id' => 'cs_fixture']);
        $this->get($url)->assertOk();
        $this->get($url)->assertOk();

        $subscription->refresh();
        $this->assertTrue($subscription->is_active);
        $this->assertSame('sub_fixture', $subscription->stripe_subscription_id);
        $this->assertTrue($subscription->expires_at->isFuture());
        $this->assertSame('pro', $this->owner->fresh()->plan);
        Mail::assertSent(BookingSubscriptionActivated::class, 1);
    }

    public function test_unpaid_checkout_cannot_activate_access(): void
    {
        $subscription = $this->subscription();
        $this->stripe(fn () => $this->paidSession($subscription, 'unpaid'));
        $this->get(route('booking.subscription.success', ['subscription' => $subscription->id, 'session_id' => 'cs_fixture']))
            ->assertRedirect(route('pricing'))->assertSessionHas('error');
        $this->assertFalse($subscription->fresh()->is_active);
        Mail::assertNothingSent();
    }

    public function test_cancel_and_resume_preserve_access_until_the_billing_period_ends(): void
    {
        $subscription = $this->subscription([
            'stripe_subscription_id' => 'sub_fixture', 'subscription_status' => 'active',
            'is_active' => true, 'status' => 'approved', 'expires_at' => now()->addMonth(),
        ]);
        $this->stripe(fn ($method, $path, $params) => array_merge([
            'id' => 'sub_fixture', 'object' => 'subscription', 'status' => 'active',
        ], $params));

        $this->from('/profile')->post(route('booking.subscription.cancel.subscription', $subscription->access_token))
            ->assertRedirect('/profile')->assertSessionHas('success');
        $this->assertTrue($subscription->fresh()->cancel_at_period_end);
        $this->assertTrue($this->owner->hasActiveBookingSubscription());
        $this->post(route('booking.subscription.resume', $subscription->access_token))->assertSessionHas('success');
        $this->assertFalse($subscription->fresh()->cancel_at_period_end);
        $this->assertSame('true', $this->requests[0][2]['cancel_at_period_end']);
        $this->assertSame('false', $this->requests[1][2]['cancel_at_period_end']);
    }

    public function test_provider_failure_does_not_record_a_successful_cancellation(): void
    {
        $subscription = $this->subscription(['stripe_subscription_id' => 'sub_fixture', 'is_active' => true]);
        $this->stripe(fn () => throw new \RuntimeException('Payment provider unavailable'));
        $this->post(route('booking.subscription.cancel.subscription', $subscription->access_token))->assertSessionHas('error');
        $this->assertFalse($subscription->fresh()->cancel_at_period_end);
        $this->assertTrue($subscription->fresh()->is_active);
    }

    private function webhook(string $type, array $object): void
    {
        $payload = json_encode(['id' => 'evt_fixture', 'object' => 'event', 'type' => $type, 'data' => ['object' => $object]], JSON_THROW_ON_ERROR);
        $timestamp = time();
        $signature = hash_hmac('sha256', $timestamp.'.'.$payload, config('services.stripe.webhook_secret'));
        $this->call('POST', '/stripe/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json', 'HTTP_STRIPE_SIGNATURE' => 't='.$timestamp.',v1='.$signature,
        ], $payload)->assertOk();
    }

    public function test_duplicate_checkout_webhook_preserves_cancellation_and_sends_one_activation_email(): void
    {
        $subscription = $this->subscription();
        $session = $this->paidSession($subscription);
        $stripeSubscription = $session['subscription'];
        $this->stripe(fn () => $stripeSubscription);
        $session['subscription'] = 'sub_fixture';
        $this->webhook('checkout.session.completed', $session);
        $subscription->refresh()->update(['cancel_at_period_end' => true]);
        $this->webhook('checkout.session.completed', $session);
        $this->assertTrue($subscription->fresh()->cancel_at_period_end);
        $this->assertTrue($subscription->fresh()->expires_at->isFuture());
        Mail::assertSent(BookingSubscriptionActivated::class, 1);
    }

    public function test_signed_subscription_events_restore_and_revoke_access(): void
    {
        $subscription = $this->subscription(['stripe_subscription_id' => 'sub_fixture']);
        $object = [
            'id' => 'sub_fixture', 'object' => 'subscription', 'status' => 'active',
            'customer' => 'cus_fixture', 'cancel_at_period_end' => true,
            'current_period_end' => now()->addMonth()->timestamp,
        ];
        $this->webhook('customer.subscription.updated', $object);
        $this->assertTrue($this->owner->hasActiveBookingSubscription());
        $this->assertTrue($subscription->fresh()->expires_at->isFuture());
        $this->webhook('customer.subscription.deleted', array_merge($object, ['status' => 'canceled']));
        $this->assertFalse($this->owner->hasActiveBookingSubscription());
    }

    public function test_security_renewal_advances_expiry_and_failed_payment_revokes_access(): void
    {
        $inquiry = SecurityInquiry::create([
            'name' => $this->owner->name, 'email' => $this->owner->email, 'package' => 'pro',
            'access_token' => Str::random(32), 'stripe_customer_id' => 'cus_fixture',
            'stripe_subscription_id' => 'sub_fixture', 'expires_at' => now()->subDay(), 'is_active' => false,
        ]);
        $this->stripe(fn () => [
            'id' => 'sub_fixture', 'object' => 'subscription', 'status' => 'active',
            'items' => ['data' => [['current_period_end' => now()->addMonth()->timestamp]]],
        ]);
        $invoice = [
            'id' => 'in_fixture', 'object' => 'invoice', 'status' => 'paid', 'customer' => 'cus_fixture',
            'subscription' => 'sub_fixture', 'billing_reason' => 'subscription_cycle',
        ];
        $this->webhook('invoice.payment_succeeded', $invoice);
        $this->assertTrue($inquiry->fresh()->expires_at->isFuture());
        $this->assertTrue($inquiry->fresh()->is_active);
        $this->webhook('invoice.payment_failed', array_merge($invoice, ['status' => 'open']));
        $this->assertFalse($inquiry->fresh()->is_active);
    }

    public function test_security_checkout_activation_cancellation_and_resume(): void
    {
        config(['services.stripe.prices.security_pro' => 'price_security_fixture']);
        $stripeSubscription = [
            'id' => 'sub_security_fixture', 'object' => 'subscription', 'customer' => 'cus_fixture',
            'status' => 'active', 'cancel_at_period_end' => false, 'current_period_end' => now()->addMonth()->timestamp,
        ];
        $this->stripe(function ($method, $path, $params) use ($stripeSubscription) {
            if ($path === '/v1/customers') {
                return ['id' => 'cus_fixture', 'object' => 'customer'];
            }
            if ($path === '/v1/checkout/sessions') {
                $this->assertSame('price_security_fixture', $params['line_items'][0]['price']);

                return ['id' => 'cs_security_fixture', 'object' => 'checkout.session', 'url' => 'https://checkout.stripe.com/c/pay/security-fixture'];
            }
            if (str_contains($path, '/checkout/sessions/')) {
                return [
                    'id' => 'cs_security_fixture', 'object' => 'checkout.session', 'mode' => 'subscription',
                    'status' => 'complete', 'payment_status' => 'paid', 'customer' => 'cus_fixture',
                    'metadata' => ['inquiry_id' => (string) SecurityInquiry::sole()->id, 'package' => 'pro'],
                    'subscription' => $stripeSubscription,
                ];
            }

            return array_merge($stripeSubscription, $params);
        });

        $this->post(route('security.checkout.post', 'pro'), ['name' => $this->owner->name, 'email' => $this->owner->email])
            ->assertRedirect('https://checkout.stripe.com/c/pay/security-fixture');
        $inquiry = SecurityInquiry::sole();
        $this->assertFalse($inquiry->is_active);
        $url = route('security.checkout.success', ['inquiry' => $inquiry->id, 'session_id' => 'cs_security_fixture']);
        $this->get($url)->assertOk();
        $this->get($url)->assertOk();
        $this->assertTrue($inquiry->fresh()->is_active);
        $this->assertTrue($inquiry->fresh()->expires_at->isFuture());
        Mail::assertSent(SecurityAccessActivated::class, 1);
        $this->post(route('security.checkout.post', 'pro'), ['name' => $this->owner->name, 'email' => $this->owner->email])
            ->assertSessionHas('error');
        $this->assertDatabaseCount('security_inquiries', 1);
        $this->post(route('security.subscription.cancel', $inquiry->access_token))->assertSessionHas('success');
        $this->assertTrue($inquiry->fresh()->cancel_at_period_end);
        $this->assertTrue($inquiry->fresh()->is_active);
        $this->post(route('security.subscription.resume', $inquiry->access_token))->assertSessionHas('success');
        $this->assertFalse($inquiry->fresh()->cancel_at_period_end);
    }
}
