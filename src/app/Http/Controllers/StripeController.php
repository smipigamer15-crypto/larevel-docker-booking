<?php

namespace App\Http\Controllers;

use App\Models\SecurityInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function checkout(Request $request, $package)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
        ]);

        $inquiry = SecurityInquiry::firstOrCreate(['email' => $request->email], [
            'name' => $request->name,
            'phone' => $request->phone,
            'company' => $request->company,
            'package' => $package,
            'status' => 'pending',
            'access_token' => Str::random(32),
        ]);

        $priceId = match ($package) {
            'pro' => env('STRIPE_PRICE_PRO'),
            'enterprise' => env('STRIPE_PRICE_ENTERPRISE'),
            default => null,
        };

        if (!$priceId) {
            return back()->with('error', 'Invalid package');
        }

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            if (!$inquiry->stripe_customer_id) {
                $customer = \Stripe\Customer::create([
                    'email' => $inquiry->email,
                    'name' => $inquiry->name,
                    'metadata' => ['inquiry_id' => $inquiry->id],
                ]);
                $inquiry->stripe_customer_id = $customer->id;
                $inquiry->save();
            }

            $session = \Stripe\Checkout\Session::create([
                'customer' => $inquiry->stripe_customer_id,
                'mode' => 'subscription',
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price' => $priceId,
                        'quantity' => 1,
                    ],
                ],
                'success_url' => route('stripe.success', ['inquiry' => $inquiry->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('stripe.cancel'),
                'metadata' => ['inquiry_id' => $inquiry->id],
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Stripe error: ' . $e->getMessage());
            return back()->with('error', 'Payment error: ' . $e->getMessage());
        }
    }

    public function success(Request $request, $inquiryId)
    {
        $inquiry = SecurityInquiry::findOrFail($inquiryId);

        $sessionId = $request->get('session_id');

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $session = \Stripe\Checkout\Session::retrieve([
                'id' => $sessionId,
                'expand' => ['subscription'],
            ]);

            if ($session->subscription) {
                $subscription = $session->subscription;

                $inquiry->stripe_subscription_id = $subscription->id;
                $inquiry->subscription_status = $subscription->status;
                $inquiry->subscription_ends_at = \Carbon\Carbon::createFromTimestamp($subscription->current_period_end);
                $inquiry->expires_at = $inquiry->subscription_ends_at;
                $inquiry->is_active = true;
                $inquiry->status = 'approved';
                $inquiry->access_activated_at = now();
                $inquiry->duration = 'monthly_subscription';
                $inquiry->duration_days = 30;
                $inquiry->save();
            }

            return view('subscription.success', compact('inquiry'));
        } catch (\Exception $e) {
            Log::error('Stripe success error: ' . $e->getMessage());
            return redirect()->route('pricing')->with('error', 'Payment verification failed');
        }
    }

    public function cancel()
    {
        return redirect()->route('pricing')->with('error', 'Payment was cancelled');
    }

    public function manage($token)
    {
        $inquiry = SecurityInquiry::where('access_token', $token)->firstOrFail();
        return view('subscription.manage', compact('inquiry'));
    }

    public function cancelSubscription($token)
    {
        $inquiry = SecurityInquiry::where('access_token', $token)->firstOrFail();

        if (!$inquiry->stripe_subscription_id) {
            return back()->with('error', 'No active subscription');
        }

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            \Stripe\Subscription::update($inquiry->stripe_subscription_id, [
                'cancel_at_period_end' => true,
            ]);

            $inquiry->cancel_at_period_end = true;
            $inquiry->save();

            return back()->with('success', 'Subscription will be cancelled at the end of the billing period.');
        } catch (\Exception $e) {
            Log::error('Cancel error: ' . $e->getMessage());
            return back()->with('error', 'Failed to cancel subscription');
        }
    }

    public function resumeSubscription($token)
    {
        $inquiry = SecurityInquiry::where('access_token', $token)->firstOrFail();

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            \Stripe\Subscription::update($inquiry->stripe_subscription_id, [
                'cancel_at_period_end' => false,
            ]);

            $inquiry->cancel_at_period_end = false;
            $inquiry->save();

            return back()->with('success', 'Subscription resumed!');
        } catch (\Exception $e) {
            Log::error('Resume error: ' . $e->getMessage());
            return back()->with('error', 'Failed to resume subscription');
        }
    }
}
