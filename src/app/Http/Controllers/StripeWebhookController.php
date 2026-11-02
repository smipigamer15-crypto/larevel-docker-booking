<?php

namespace App\Http\Controllers;

use App\Mail\BookingSubscriptionActivated;
use App\Mail\BookingSubscriptionRenewed;
use App\Models\BookingSubscription;
use App\Models\SecurityInquiry;
use App\Models\User;
use App\Services\StripeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Subscription;

class StripeWebhookController extends Controller
{
    public function handle(Request $request, StripeService $stripe)
    {
        if (! $request->header('Stripe-Signature')) {
            return response('Webhook error', 400);
        }

        try {
            $event = $stripe->handleWebhook($request->getContent(), $request->header('Stripe-Signature'));
        } catch (\Exception $e) {
            Log::error('Webhook error: '.$e->getMessage());

            return response('Webhook error', 400);
        }

        Log::info('Stripe webhook: '.$event->type);

        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutCompleted($event->data->object);
                break;

            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;

            case 'invoice.payment_succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;

            case 'invoice.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;
        }

        return response('OK', 200);
    }

    protected function handleCheckoutCompleted($session)
    {
        if ($session->mode !== 'subscription' || $session->payment_status !== 'paid' || ! $session->subscription) {
            return;
        }

        $inquiryId = $session->metadata->security_inquiry_id ?? $session->metadata->inquiry_id ?? null;
        if ($inquiryId) {
            $inquiry = SecurityInquiry::find($inquiryId);
            if ($inquiry && $inquiry->stripe_customer_id === $session->customer && $inquiry->package === ($session->metadata->package ?? null)) {
                if ($inquiry->stripe_subscription_id === $session->subscription && $inquiry->access_activated_at !== null) {
                    return;
                }
                $this->activateCheckoutSubscription($inquiry, $session);

                return;
            }
        }

        $bookingSubId = $session->metadata->booking_subscription_id ?? null;
        if ($bookingSubId) {
            $subscription = BookingSubscription::find($bookingSubId);
            if ($subscription && $subscription->stripe_customer_id === $session->customer && $subscription->package === ($session->metadata->package ?? null)) {
                if ($subscription->stripe_subscription_id === $session->subscription && $subscription->access_activated_at !== null) {
                    return;
                }
                if (! $this->activateCheckoutSubscription($subscription, $session)) {
                    return;
                }

                try {
                    Mail::to($subscription->email)->send(new BookingSubscriptionActivated($subscription));
                } catch (\Exception $e) {
                    Log::error('Subscription email failed: '.$e->getMessage());
                }

                $user = User::where('email', $subscription->email)->first();
                if ($user) {
                    $user->plan = $subscription->package;
                    $user->save();
                }
            }
        }
    }

    private function activateCheckoutSubscription(SecurityInquiry|BookingSubscription $record, $session): bool
    {
        $subscription = Subscription::retrieve($session->subscription);
        if ($subscription->customer !== $record->stripe_customer_id || ! in_array($subscription->status, ['active', 'trialing'], true)) {
            return false;
        }
        $periodEnd = $subscription->current_period_end ?? $subscription->items->data[0]->current_period_end ?? null;
        if (! $periodEnd) {
            throw new \UnexpectedValueException('The subscription billing period is missing.');
        }

        $record->fill([
            'stripe_subscription_id' => $subscription->id,
            'subscription_status' => $subscription->status,
            'status' => 'approved',
            'is_active' => true,
            'cancel_at_period_end' => $subscription->cancel_at_period_end ?? false,
            'access_activated_at' => now(),
            'subscription_ends_at' => Carbon::createFromTimestamp($periodEnd),
            'expires_at' => Carbon::createFromTimestamp($periodEnd),
        ])->save();

        return true;
    }

    protected function handleSubscriptionUpdated($subscription)
    {
        $periodEnd = $subscription->current_period_end ?? $subscription->items->data[0]->current_period_end ?? null;

        $inquiry = SecurityInquiry::where('stripe_subscription_id', $subscription->id)->first();
        if ($inquiry) {
            $inquiry->subscription_status = $subscription->status;
            $inquiry->is_active = in_array($subscription->status, ['active', 'trialing'], true);
            if ($inquiry->is_active) {
                $inquiry->status = 'approved';
            }
            $inquiry->cancel_at_period_end = $subscription->cancel_at_period_end;
            if ($periodEnd) {
                $inquiry->subscription_ends_at = Carbon::createFromTimestamp($periodEnd);
                $inquiry->expires_at = $inquiry->subscription_ends_at;
            }
            $inquiry->save();

            return;
        }

        $bookingSub = BookingSubscription::where('stripe_subscription_id', $subscription->id)->first();
        if ($bookingSub) {
            $bookingSub->subscription_status = $subscription->status;
            $bookingSub->is_active = in_array($subscription->status, ['active', 'trialing'], true);
            if ($bookingSub->is_active) {
                $bookingSub->status = 'approved';
            }
            $bookingSub->cancel_at_period_end = $subscription->cancel_at_period_end;
            if ($periodEnd) {
                $bookingSub->subscription_ends_at = Carbon::createFromTimestamp($periodEnd);
                $bookingSub->expires_at = $bookingSub->subscription_ends_at;
            }
            $bookingSub->save();
        }
    }

    protected function handleSubscriptionDeleted($subscription)
    {
        $inquiry = SecurityInquiry::where('stripe_subscription_id', $subscription->id)->first();
        if ($inquiry) {
            $inquiry->subscription_status = 'cancelled';
            $inquiry->is_active = false;
            $inquiry->status = 'deactivated';
            $inquiry->save();

            return;
        }

        $bookingSub = BookingSubscription::where('stripe_subscription_id', $subscription->id)->first();
        if ($bookingSub) {
            $bookingSub->subscription_status = 'cancelled';
            $bookingSub->is_active = false;
            $bookingSub->status = 'deactivated';
            $bookingSub->save();
        }
    }

    protected function handlePaymentSucceeded($invoice)
    {
        $subscriptionId = $invoice->subscription ?? $invoice->parent->subscription_details->subscription ?? null;
        if (! $subscriptionId || $invoice->status !== 'paid') {
            return;
        }

        $inquiry = SecurityInquiry::where('stripe_subscription_id', $subscriptionId)
            ->where('stripe_customer_id', $invoice->customer)->first();
        if ($inquiry) {
            $inquiry->subscription_status = 'active';
            $inquiry->is_active = true;
            $inquiry->status = 'approved';
            if ($invoice->billing_reason === 'subscription_cycle') {
                Stripe::setApiKey(config('services.stripe.secret'));
                $stripeSub = Subscription::retrieve($inquiry->stripe_subscription_id);
                $periodEnd = $stripeSub->current_period_end ?? $stripeSub->items->data[0]->current_period_end ?? null;
                if ($periodEnd) {
                    $inquiry->subscription_ends_at = Carbon::createFromTimestamp($periodEnd);
                    $inquiry->expires_at = $inquiry->subscription_ends_at;
                }
            }
            $inquiry->save();

            return;
        }

        $bookingSub = BookingSubscription::where('stripe_subscription_id', $subscriptionId)
            ->where('stripe_customer_id', $invoice->customer)->first();
        if ($bookingSub) {
            $bookingSub->subscription_status = 'active';
            $bookingSub->is_active = true;
            $bookingSub->status = 'approved';

            if ($invoice->billing_reason === 'subscription_cycle') {
                try {
                    Stripe::setApiKey(config('services.stripe.secret'));
                    $stripeSub = Subscription::retrieve($bookingSub->stripe_subscription_id);

                    $periodEnd = $stripeSub->current_period_end ?? $stripeSub->items->data[0]->current_period_end ?? null;

                    if ($periodEnd) {
                        $bookingSub->subscription_ends_at = Carbon::createFromTimestamp($periodEnd);
                        $bookingSub->expires_at = $bookingSub->subscription_ends_at;
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to update dates on renew: '.$e->getMessage());
                }

                try {
                    Mail::to($bookingSub->email)
                        ->send(new BookingSubscriptionRenewed($bookingSub, $invoice->amount_paid / 100));
                } catch (\Exception $e) {
                    Log::error('Renewal email failed: '.$e->getMessage());
                }
            }

            $bookingSub->save();
        }
    }

    protected function handlePaymentFailed($invoice)
    {
        $subscriptionId = $invoice->subscription ?? $invoice->parent->subscription_details->subscription ?? null;
        if (! $subscriptionId) {
            return;
        }

        $inquiry = SecurityInquiry::where('stripe_subscription_id', $subscriptionId)
            ->where('stripe_customer_id', $invoice->customer)->first();
        if ($inquiry) {
            $inquiry->subscription_status = 'past_due';
            $inquiry->is_active = false;
            $inquiry->save();

            return;
        }

        $bookingSub = BookingSubscription::where('stripe_subscription_id', $subscriptionId)
            ->where('stripe_customer_id', $invoice->customer)->first();
        if ($bookingSub) {
            $bookingSub->subscription_status = 'past_due';
            $bookingSub->is_active = false;
            $bookingSub->save();
        }
    }
}
