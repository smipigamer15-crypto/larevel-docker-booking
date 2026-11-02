<?php

namespace Tests\Unit;

use App\Models\BookingSubscription;
use App\Services\StripeService;
use PHPUnit\Framework\Attributes\DataProvider;
use Stripe\Checkout\Session;
use Tests\TestCase;

class StripeCheckoutValidationTest extends TestCase
{
    private static function paidSession(): array
    {
        return [
            'id' => 'cs_test',
            'mode' => 'subscription',
            'status' => 'complete',
            'payment_status' => 'paid',
            'customer' => 'cus_test',
            'metadata' => ['booking_subscription_id' => '12', 'package' => 'pro'],
            'subscription' => ['id' => 'sub_test', 'object' => 'subscription', 'status' => 'active', 'current_period_end' => 1800000000],
        ];
    }

    public function test_paid_checkout_must_belong_to_the_record_and_customer(): void
    {
        $record = (new BookingSubscription)->forceFill(['id' => 12, 'stripe_customer_id' => 'cus_test', 'package' => 'pro']);
        $session = Session::constructFrom(self::paidSession());

        $this->assertSame($session, app(StripeService::class)->validatePaidCheckoutSession($session, 'booking_subscription_id', $record));
    }

    #[DataProvider('invalidSessions')]
    public function test_unpaid_or_unrelated_sessions_are_rejected(array $changes): void
    {
        $record = (new BookingSubscription)->forceFill(['id' => 12, 'stripe_customer_id' => 'cus_test', 'package' => 'pro']);
        $session = Session::constructFrom(array_replace(self::paidSession(), $changes));

        $this->expectException(\UnexpectedValueException::class);
        app(StripeService::class)->validatePaidCheckoutSession($session, 'booking_subscription_id', $record);
    }

    public static function invalidSessions(): array
    {
        return [
            'unpaid' => [['payment_status' => 'unpaid']],
            'incomplete' => [['status' => 'open']],
            'wrong mode' => [['mode' => 'payment']],
            'another order' => [['metadata' => ['booking_subscription_id' => '99']]],
            'missing metadata' => [['metadata' => []]],
            'another customer' => [['customer' => 'cus_other']],
            'wrong package' => [['metadata' => ['booking_subscription_id' => '12', 'package' => 'business']]],
            'no subscription' => [['subscription' => null]],
            'inactive subscription' => [['subscription' => ['id' => 'sub_test', 'object' => 'subscription', 'status' => 'canceled']]],
            'missing billing period' => [['subscription' => ['id' => 'sub_test', 'object' => 'subscription', 'status' => 'active']]],
        ];
    }
}
