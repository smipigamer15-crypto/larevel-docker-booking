<?php

namespace App\Http\Controllers;

use App\Mail\SecurityAccessActivated;
use App\Mail\SecurityInquiryConfirmation;
use App\Models\SecurityInquiry;
use App\Services\MonitoringService;
use App\Services\StripeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Stripe;
use Stripe\Subscription;

class SecurityController extends Controller
{
    public function index()
    {
        return view('features.security');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'message' => 'nullable|string',
            'package' => 'required|string|in:basic,pro,enterprise,custom',
        ]);

        $inquiry = SecurityInquiry::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
            'message' => $request->message,
            'package' => $request->package,
            'status' => 'pending',
            'access_token' => Str::random(32),
        ]);

        MonitoringService::info('security_inquiry_created', 'New security inquiry from '.$inquiry->email, [
            'inquiry_id' => $inquiry->id,
            'email' => $inquiry->email,
            'package' => $inquiry->package,
        ]);

        try {
            Mail::to($request->email)->send(new SecurityInquiryConfirmation($inquiry));
        } catch (\Exception $e) {
            Log::error('Email sending failed: '.$e->getMessage());
            MonitoringService::error('email_failed', 'Security inquiry confirmation email failed: '.$e->getMessage(), ['email' => $request->email, 'inquiry_id' => $inquiry->id]);
        }

        return redirect()->back()->with('success', 'Thank you! We will contact you shortly with security package details.');
    }

    public function checkoutPage($package)
    {
        if (! in_array($package, ['pro', 'enterprise'])) {
            return redirect()->route('security');
        }

        return view('security.checkout', compact('package'));
    }

    public function checkout(Request $request, $package)
    {
        abort_unless($request->user()->email === $request->input('email'), 403);
        abort_unless(in_array($package, ['pro', 'enterprise'], true), 404);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
        ]);

        if (SecurityInquiry::where('email', $request->user()->email)
            ->whereNotNull('stripe_subscription_id')->where('is_active', true)->exists()) {
            return back()->with('error', 'Manage your existing subscription before starting another checkout.');
        }

        $inquiry = SecurityInquiry::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company' => $request->company,
            'package' => $package,
            'status' => 'pending',
            'access_token' => Str::random(32),
        ]);

        $priceId = match ($package) {
            'pro' => config('services.stripe.prices.security_pro'),
            'enterprise' => config('services.stripe.prices.security_enterprise'),
            default => null,
        };

        if (! $priceId) {
            MonitoringService::error('stripe_price_missing', 'Stripe price ID not configured for package: '.$package, ['package' => $package]);

            return back()->with('error', 'Invalid package selected. Please contact support.');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $customer = Customer::create([
                'email' => $inquiry->email,
                'name' => $inquiry->name,
                'phone' => $inquiry->phone,
                'metadata' => [
                    'inquiry_id' => $inquiry->id,
                    'type' => 'security',
                ],
            ]);
            $inquiry->stripe_customer_id = $customer->id;
            $inquiry->save();

            $session = Session::create([
                'customer' => $inquiry->stripe_customer_id,
                'mode' => 'subscription',
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price' => $priceId,
                        'quantity' => 1,
                    ],
                ],
                'success_url' => route('security.checkout.success', ['inquiry' => $inquiry->id]).'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('security').'#packages',
                'metadata' => [
                    'inquiry_id' => $inquiry->id,
                    'package' => $package,
                ],
                'subscription_data' => [
                    'metadata' => ['security_inquiry_id' => $inquiry->id, 'package' => $package],
                ],
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Stripe security checkout error: '.$e->getMessage());

            return back()->with('error', 'Unable to start payment. Please try again.');
        }
    }

    public function checkoutSuccess(Request $request, $inquiryId)
    {
        $request->validate(['session_id' => 'required|string|max:255']);

        $inquiry = SecurityInquiry::findOrFail($inquiryId);
        abort_unless($request->user()->email === $inquiry->email, 403);
        $sessionId = $request->get('session_id');

        if (! $sessionId) {
            return redirect()->route('security')->with('error', 'Invalid session');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $session = app(StripeService::class)->retrievePaidCheckoutSession($sessionId, 'inquiry_id', $inquiry);

            if ($session->payment_status !== 'paid' || ! $session->subscription) {
                return redirect()->route('security')->with('error', 'Payment not completed');
            }

            $subscriptionId = is_string($session->subscription) ? $session->subscription : $session->subscription->id;

            $subscription = Subscription::retrieve([
                'id' => $subscriptionId,
                'expand' => ['items.data.price'],
            ]);

            $currentPeriodEnd = null;

            if (isset($subscription->items->data[0]->current_period_end)) {
                $currentPeriodEnd = $subscription->items->data[0]->current_period_end;
            } elseif (isset($subscription->current_period_end)) {
                $currentPeriodEnd = $subscription->current_period_end;
            }

            if (! $currentPeriodEnd) {
                $currentPeriodEnd = now()->addDays(30)->timestamp;
            }

            $alreadyActivated = $inquiry->stripe_subscription_id === $subscription->id && $inquiry->access_activated_at !== null;
            $inquiry->stripe_subscription_id = $subscription->id;
            $inquiry->subscription_status = $subscription->status;
            $inquiry->cancel_at_period_end = $subscription->cancel_at_period_end ?? false;
            $inquiry->stripe_price_id = $subscription->items->data[0]->price->id ?? null;
            $inquiry->subscription_ends_at = Carbon::createFromTimestamp($currentPeriodEnd);
            $inquiry->expires_at = $inquiry->subscription_ends_at;
            $inquiry->is_active = true;
            $inquiry->status = 'approved';
            $inquiry->access_activated_at ??= now();
            $inquiry->duration = 'monthly';
            $inquiry->duration_days = 30;
            $inquiry->save();

            try {
                if (! $alreadyActivated) {
                    Mail::to($inquiry->email)->send(new SecurityAccessActivated($inquiry));
                }
            } catch (\Exception $e) {
                Log::error('Activation email failed: '.$e->getMessage());
            }

            return view('security.checkout-success', compact('inquiry'));
        } catch (\Exception $e) {
            Log::error('Stripe security success error: '.$e->getMessage());

            return redirect()->route('security')->with('error', 'Payment verification failed');
        }
    }

    public function cancelSubscription($token)
    {
        $inquiry = SecurityInquiry::where('access_token', $token)->firstOrFail();
        abort_unless(request()->user()->email === $inquiry->email, 403);

        if (! $inquiry->stripe_subscription_id) {
            return back()->with('error', 'No active subscription');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            Subscription::update($inquiry->stripe_subscription_id, [
                'cancel_at_period_end' => true,
            ]);

            $inquiry->cancel_at_period_end = true;
            $inquiry->save();

            return back()->with('success', 'Subscription will be cancelled at the end of the billing period.');
        } catch (\Exception $e) {
            Log::error('Cancel subscription error: '.$e->getMessage());

            return back()->with('error', 'Failed to cancel subscription');
        }
    }

    public function resumeSubscription($token)
    {
        $inquiry = SecurityInquiry::where('access_token', $token)->firstOrFail();
        abort_unless(request()->user()->email === $inquiry->email, 403);

        if (! $inquiry->stripe_subscription_id) {
            return back()->with('error', 'No active subscription');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            Subscription::update($inquiry->stripe_subscription_id, [
                'cancel_at_period_end' => false,
            ]);

            $inquiry->cancel_at_period_end = false;
            $inquiry->save();

            return back()->with('success', 'Subscription resumed!');
        } catch (\Exception $e) {
            Log::error('Resume subscription error: '.$e->getMessage());

            return back()->with('error', 'Failed to resume subscription');
        }
    }

    public function clientStatus($token)
    {
        $inquiry = SecurityInquiry::where('access_token', $token)->firstOrFail();

        if ($inquiry->is_active && $inquiry->is_expired) {
            $inquiry->is_active = false;
            $inquiry->status = 'expired';
            $inquiry->save();
        }

        return view('security.client-status', compact('inquiry'));
    }

    public function checkStatus(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $inquiry = SecurityInquiry::where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if (! $inquiry) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'No active security package found for this email',
            ]);
        }

        return response()->json([
            'status' => 'found',
            'inquiry' => [
                'id' => $inquiry->id,
                'package' => $inquiry->package_label,
                'status' => $inquiry->status_label,
                'activated_at' => $inquiry->access_activated_at?->format('M d, Y H:i'),
            ],
        ]);
    }

    public function activateAccess($token)
    {
        $inquiry = SecurityInquiry::where('access_token', $token)->firstOrFail();

        if ($inquiry->is_active) {
            return redirect()->route('security.client.status', $token)
                ->with('info', 'Your security package is already activated!');
        }

        abort(403, 'Activation requires approval or verified payment.');
    }
}
