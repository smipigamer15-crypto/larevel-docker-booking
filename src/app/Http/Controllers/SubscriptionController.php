<?php

namespace App\Http\Controllers;

use App\Models\SecurityInquiry;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    protected $stripe;

    public function __construct(StripeService $stripe)
    {
        $this->stripe = $stripe;
    }

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
            'access_token' => \Str::random(32),
        ]);

        $priceId = $inquiry->package_stripe_price_id;
        if (!$priceId) {
            return back()->with('error', 'Invalid package selected');
        }

        try {
            $session = $this->stripe->createCheckoutSession($inquiry, $priceId, route('subscription.success', ['inquiry' => $inquiry->id]), route('subscription.cancel', ['inquiry' => $inquiry->id]));

            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Stripe checkout error: ' . $e->getMessage());
            return back()->with('error', 'Payment error: ' . $e->getMessage());
        }
    }

    public function success($inquiryId)
    {
        $inquiry = SecurityInquiry::findOrFail($inquiryId);
        return view('subscription.success', compact('inquiry'));
    }

    public function cancel($inquiryId)
    {
        $inquiry = SecurityInquiry::findOrFail($inquiryId);
        return redirect()->route('pricing')->with('error', 'Subscription was cancelled.');
    }

    public function manage($token)
    {
        $inquiry = SecurityInquiry::where('access_token', $token)->firstOrFail();
        return view('subscription.manage', compact('inquiry'));
    }

    public function cancelSubscription($token)
    {
        $inquiry = SecurityInquiry::where('access_token', $token)->firstOrFail();

        if ($this->stripe->cancelSubscription($inquiry)) {
            return back()->with('success', 'Subscription will be cancelled at the end of the billing period.');
        }

        return back()->with('error', 'Unable to cancel subscription.');
    }

    public function resumeSubscription($token)
    {
        $inquiry = SecurityInquiry::where('access_token', $token)->firstOrFail();

        if ($this->stripe->resumeSubscription($inquiry)) {
            return back()->with('success', 'Subscription resumed successfully.');
        }

        return back()->with('error', 'Unable to resume subscription.');
    }
}
