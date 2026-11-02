<?php

namespace App\Services;

use App\Models\BookingSubscription;
use App\Models\SecurityInquiry;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\Subscription;
use Stripe\Webhook;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createOrGetCustomer(SecurityInquiry $inquiry)
    {
        if ($inquiry->stripe_customer_id) {
            try {
                return Customer::retrieve($inquiry->stripe_customer_id);
            } catch (\Exception $e) {

            }
        }

        $customer = Customer::create([
            'email' => $inquiry->email,
            'name' => $inquiry->name,
            'metadata' => [
                'security_inquiry_id' => $inquiry->id,
                'package' => $inquiry->package,
            ],
        ]);

        $inquiry->stripe_customer_id = $customer->id;
        $inquiry->save();

        return $customer;
    }

    public function createCheckoutSession(SecurityInquiry $inquiry, string $priceId, string $successUrl, string $cancelUrl)
    {
        $customer = $this->createOrGetCustomer($inquiry);

        $session = Session::create([
            'customer' => $customer->id,
            'mode' => 'subscription',
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price' => $priceId,
                    'quantity' => 1,
                ],
            ],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'security_inquiry_id' => $inquiry->id,
                'package' => $inquiry->package,
            ],
            'subscription_data' => [
                'metadata' => [
                    'security_inquiry_id' => $inquiry->id,
                    'package' => $inquiry->package,
                ],
            ],
        ]);

        return $session;
    }

    public function cancelSubscription(SecurityInquiry $inquiry, bool $immediately = false)
    {
        if (! $inquiry->stripe_subscription_id) {
            return false;
        }

        if ($immediately) {
            Subscription::retrieve($inquiry->stripe_subscription_id)->cancel();
        } else {
            Subscription::update($inquiry->stripe_subscription_id, [
                'cancel_at_period_end' => true,
            ]);
            $inquiry->cancel_at_period_end = true;
            $inquiry->save();
        }

        return true;
    }

    public function resumeSubscription(SecurityInquiry $inquiry)
    {
        if (! $inquiry->stripe_subscription_id) {
            return false;
        }

        Subscription::update($inquiry->stripe_subscription_id, [
            'cancel_at_period_end' => false,
        ]);

        $inquiry->cancel_at_period_end = false;
        $inquiry->save();

        return true;
    }

    public function handleWebhook(string $payload, string $signature)
    {
        if (! config('services.stripe.webhook_secret')) {
            throw new \RuntimeException('The Stripe webhook signing secret is not configured.');
        }

        $event = Webhook::constructEvent($payload, $signature, config('services.stripe.webhook_secret'));

        return $event;
    }

    public function retrievePaidCheckoutSession(string $sessionId, string $metadataKey, $record): Session
    {
        $session = Session::retrieve(['id' => $sessionId, 'expand' => ['subscription']]);

        return $this->validatePaidCheckoutSession($session, $metadataKey, $record);
    }

    public function validatePaidCheckoutSession(Session $session, string $metadataKey, $record): Session
    {
        if ($session->mode !== 'subscription' || $session->status !== 'complete' || $session->payment_status !== 'paid' || (string) ($session->metadata[$metadataKey] ?? '') !== (string) $record->id || ($session->metadata->package ?? null) !== $record->package || ! $record->stripe_customer_id || $session->customer !== $record->stripe_customer_id || ! $session->subscription || ! in_array($session->subscription->status, ['active', 'trialing'], true)) {
            throw new \UnexpectedValueException('Checkout session does not match a paid subscription.');
        }

        $periodEnd = $session->subscription->current_period_end ?? $session->subscription->items->data[0]->current_period_end ?? null;
        if (! is_numeric($periodEnd) || $periodEnd <= 0) {
            throw new \UnexpectedValueException('The subscription billing period is missing.');
        }

        return $session;
    }

    public function createBookingCheckoutSession(BookingSubscription $subscription, string $priceId, string $successUrl, string $cancelUrl)
    {
        if ($subscription->stripe_customer_id) {
            try {
                $customer = Customer::retrieve($subscription->stripe_customer_id);
            } catch (\Exception $e) {
                $customer = Customer::create([
                    'email' => $subscription->email,
                    'name' => $subscription->name,
                    'metadata' => ['booking_subscription_id' => $subscription->id],
                ]);
                $subscription->stripe_customer_id = $customer->id;
                $subscription->save();
            }
        } else {
            $customer = Customer::create([
                'email' => $subscription->email,
                'name' => $subscription->name,
                'metadata' => ['booking_subscription_id' => $subscription->id],
            ]);
            $subscription->stripe_customer_id = $customer->id;
            $subscription->save();
        }

        $session = Session::create([
            'customer' => $customer->id,
            'mode' => 'subscription',
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price' => $priceId,
                    'quantity' => 1,
                ],
            ],
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'booking_subscription_id' => $subscription->id,
                'package' => $subscription->package,
                'type' => 'booking',
            ],
            'subscription_data' => [
                'metadata' => [
                    'booking_subscription_id' => $subscription->id,
                    'package' => $subscription->package,
                    'type' => 'booking',
                ],
            ],
        ]);

        return $session;
    }
}
