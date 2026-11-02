<?php

namespace App\Http\Controllers;

use App\Mail\BookingSubscriptionActivated;
use App\Models\BookingSubscription;
use App\Models\User;
use App\Services\StripeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Subscription;

class BookingSubscriptionController extends Controller
{
    protected StripeService $stripe;

    public function __construct(StripeService $stripe)
    {
        $this->stripe = $stripe;
    }

    public function checkoutPage($package)
    {
        if (! in_array($package, ['pro', 'business', 'enterprise'])) {
            abort(404);
        }

        return view('subscription.booking-checkout', compact('package'));
    }

    public function checkout(Request $request, $package)
    {
        abort_unless($request->user()->email === $request->input('email'), 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
        ]);

        $priceId = match ($package) {
            'pro' => config('services.stripe.prices.booking_pro'),
            'business', 'enterprise' => config('services.stripe.prices.booking_enterprise'),
            default => null,
        };

        if (! $priceId) {
            return back()->with('error', 'Invalid package');
        }

        $subscription = BookingSubscription::firstOrCreate(['email' => $request->email], [
            'name' => $request->name,
            'phone' => $request->phone,
            'company' => $request->company,
            'package' => $package,
            'status' => 'pending',
            'access_token' => Str::random(32),
        ]);

        if ($subscription->is_active || $subscription->stripe_subscription_id && $subscription->package !== $package) {
            return back()->with('error', 'Manage your existing subscription before starting another checkout.');
        }

        if ($subscription->package !== $package) {
            $subscription->update(['package' => $package]);
        }

        try {
            $session = $this->stripe->createBookingCheckoutSession($subscription, $priceId, route('booking.subscription.success', ['subscription' => $subscription->id]).'?session_id={CHECKOUT_SESSION_ID}', route('booking.subscription.cancel'));

            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Booking Stripe checkout error: '.$e->getMessage());

            return back()->with('error', 'Unable to start payment. Please try again.');
        }
    }

    public function success(Request $request, $subscriptionId)
    {
        $request->validate(['session_id' => 'required|string|max:255']);

        $subscription = BookingSubscription::findOrFail($subscriptionId);
        abort_unless($request->user()->email === $subscription->email, 403);
        $sessionId = $request->get('session_id');

        if (! $sessionId) {
            return redirect()->route('pricing')->with('error', 'Missing session ID');
        }

        try {
            $session = $this->stripe->retrievePaidCheckoutSession($sessionId, 'booking_subscription_id', $subscription);

            if ($session->subscription) {
                $sub = $session->subscription;
                $alreadyActivated = $subscription->stripe_subscription_id === $sub->id && $subscription->access_activated_at !== null;

                $subscription->stripe_subscription_id = $sub->id;
                $subscription->stripe_customer_id = $sub->customer;
                $subscription->subscription_status = $sub->status;

                $periodEnd = $sub->current_period_end ?? $sub->items->data[0]->current_period_end ?? null;

                if ($periodEnd) {
                    $subscription->subscription_ends_at = Carbon::createFromTimestamp($periodEnd);
                    $subscription->expires_at = $subscription->subscription_ends_at;
                }

                $subscription->is_active = true;
                $subscription->status = 'approved';
                $subscription->access_activated_at ??= now();
                $subscription->save();

                BookingSubscription::where('email', $subscription->email)
                    ->where('id', '!=', $subscription->id)
                    ->where('is_active', true)
                    ->update([
                        'is_active' => false,
                        'status' => 'deactivated',
                        'cancel_at_period_end' => true,
                    ]);

                try {
                    if (! $alreadyActivated) {
                        Mail::to($subscription->email)
                            ->send(new BookingSubscriptionActivated($subscription));
                    }
                } catch (\Exception $e) {
                    Log::error('Subscription email failed: '.$e->getMessage());
                }

                $user = User::where('email', $subscription->email)->first();

                if ($user) {
                    $user->plan = $subscription->package;
                    $user->save();
                }
            }

            return view('subscription.booking-success', compact('subscription'));
        } catch (\Exception $e) {
            Log::error('Booking Stripe success error: '.$e->getMessage());

            return redirect()->route('pricing')->with('error', 'Payment verification failed');
        }
    }

    public function cancel()
    {
        return redirect()->route('pricing')->with('error', 'Payment was cancelled');
    }

    public function manage($token)
    {
        $subscription = BookingSubscription::where('access_token', $token)->firstOrFail();
        abort_unless(request()->user()->email === $subscription->email, 403);

        return view('subscription.booking-manage', compact('subscription'));
    }

    public function downloadReport($token)
    {
        $subscription = BookingSubscription::where('access_token', $token)->firstOrFail();
        abort_unless(request()->user()->email === $subscription->email, 403);

        $pdf = Pdf::loadView('subscription.booking-report', [
            'subscription' => $subscription,
        ]);

        return $pdf->download('booking-subscription-'.$subscription->id.'.pdf');
    }

    public function cancelSubscription($token)
    {
        $subscription = BookingSubscription::where('access_token', $token)->firstOrFail();
        abort_unless(request()->user()->email === $subscription->email, 403);

        if (! $subscription->stripe_subscription_id) {
            return back()->with('error', 'No active subscription');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            Subscription::update($subscription->stripe_subscription_id, [
                'cancel_at_period_end' => true,
            ]);

            $subscription->cancel_at_period_end = true;
            $subscription->save();

            return back()->with('success', 'Subscription will be cancelled at the end of the billing period.');
        } catch (\Exception $e) {
            Log::error('Booking cancel error: '.$e->getMessage());

            return back()->with('error', 'Failed to cancel subscription');
        }
    }

    public function resumeSubscription($token)
    {
        $subscription = BookingSubscription::where('access_token', $token)->firstOrFail();
        abort_unless(request()->user()->email === $subscription->email, 403);

        if (! $subscription->stripe_subscription_id) {
            return back()->with('error', 'No active subscription');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            Subscription::update($subscription->stripe_subscription_id, [
                'cancel_at_period_end' => false,
            ]);

            $subscription->cancel_at_period_end = false;
            $subscription->save();

            return back()->with('success', 'Subscription resumed!');
        } catch (\Exception $e) {
            Log::error('Booking resume error: '.$e->getMessage());

            return back()->with('error', 'Failed to resume subscription');
        }
    }
}
