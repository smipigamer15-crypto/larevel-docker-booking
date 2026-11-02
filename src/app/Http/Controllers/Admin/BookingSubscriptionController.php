<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Subscription as StripeSubscription;

class BookingSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = BookingSubscription::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('package')) {
            $query->where('package', $request->package);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            });
        }

        $subscriptions = $query->latest()->paginate(20);

        $stats = [
            'total' => BookingSubscription::count(),
            'active' => BookingSubscription::where('is_active', true)->count(),
            'pending' => BookingSubscription::where('status', 'pending')->count(),
            'cancelling' => BookingSubscription::where('cancel_at_period_end', true)->count(),
            'pro' => BookingSubscription::where('package', 'pro')->count(),
            'enterprise' => BookingSubscription::where('package', 'enterprise')->count(),
            'mrr' => BookingSubscription::where('is_active', true)
                ->get()
                ->sum(fn($s) => $s->package === 'pro' ? 29 : 99),
        ];

        return view('admin.subscriptions.index', compact('subscriptions', 'stats'));
    }

    public function show($id)
    {
        $subscription = BookingSubscription::findOrFail($id);
        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,deactivated',
            'is_active' => 'boolean',
        ]);

        $subscription = BookingSubscription::findOrFail($id);
        $subscription->status = $request->status;
        $subscription->is_active = $request->boolean('is_active');

        if ($request->status === 'approved' && !$subscription->access_activated_at) {
            $subscription->access_activated_at = now();
        }

        $subscription->save();

        return back()->with('success', 'Subscription updated successfully.');
    }

    public function activate($id)
    {
        $subscription = BookingSubscription::findOrFail($id);

        $subscription->status = 'approved';
        $subscription->is_active = true;

        if (!$subscription->subscription_ends_at || $subscription->subscription_ends_at->isPast()) {
            $subscription->subscription_ends_at = now()->addMonth();
            $subscription->expires_at = $subscription->subscription_ends_at;
        }

        $subscription->access_activated_at = now();
        $subscription->cancel_at_period_end = false;
        $subscription->save();

        return back()->with('success', 'Subscription activated.');
    }

    public function deactivate($id)
    {
        $subscription = BookingSubscription::findOrFail($id);
        $subscription->status = 'deactivated';
        $subscription->is_active = false;
        $subscription->save();

        return back()->with('success', 'Subscription deactivated.');
    }

    public function regenerateToken($id)
    {
        $subscription = BookingSubscription::findOrFail($id);
        $subscription->access_token = \Illuminate\Support\Str::random(32);
        $subscription->save();

        return back()->with('success', 'Access token regenerated.');
    }

    public function extend(Request $request, $id)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        $subscription = BookingSubscription::findOrFail($id);

        $base = $subscription->subscription_ends_at && $subscription->subscription_ends_at->isFuture() ? $subscription->subscription_ends_at : now();

        $subscription->subscription_ends_at = $base->copy()->addDays((int) $request->days);
        $subscription->expires_at = $subscription->subscription_ends_at;
        $subscription->save();

        return back()->with('success', "Extended by {$request->days} days.");
    }

    public function cancelSubscription($id)
    {
        $subscription = BookingSubscription::findOrFail($id);

        if (!$subscription->stripe_subscription_id) {
            return back()->with('error', 'No Stripe subscription attached.');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            StripeSubscription::update($subscription->stripe_subscription_id, [
                'cancel_at_period_end' => true,
            ]);

            $subscription->cancel_at_period_end = true;
            $subscription->save();

            return back()->with('success', 'Subscription will cancel at period end.');
        } catch (\Exception $e) {
            Log::error('Admin cancel error: ' . $e->getMessage());
            return back()->with('error', 'Failed: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $subscription = BookingSubscription::findOrFail($id);
        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription deleted.');
    }
}
