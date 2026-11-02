<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityInquiry;
use App\Services\MonitoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\SecurityInquiryConfirmation;
use App\Mail\SecurityDeactivated;
use App\Mail\SecurityAccessActivated;

class SecurityController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $query = SecurityInquiry::query();

        if ($status !== 'all' && $status) {
            $query->where('status', $status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if ($request->filled('package')) {
            $query->where('package', $request->package);
        }

        $inquiries = $query->latest()->paginate(20)->appends($request->query());

        $stats = [
            'total' => SecurityInquiry::count(),
            'pending' => SecurityInquiry::where('status', 'pending')->count(),
            'contacted' => SecurityInquiry::where('status', 'contacted')->count(),
            'approved' => SecurityInquiry::where('status', 'approved')->count(),
            'rejected' => SecurityInquiry::where('status', 'rejected')->count(),
            'deactivated' => SecurityInquiry::where('status', 'deactivated')->count(),
            'subscriptions' => SecurityInquiry::whereNotNull('stripe_subscription_id')->count(),
        ];

        $packages = [
            'basic' => 'Free Security Assessment',
            'pro' => 'Pro Security Plan',
            'enterprise' => 'Enterprise Plan',
            'custom' => 'Custom Solution',
        ];

        return view('admin.security.index', compact('inquiries', 'stats', 'packages', 'status'));
    }

    public function show($id)
    {
        $inquiry = SecurityInquiry::findOrFail($id);
        return view('admin.security.show', compact('inquiry'));
    }

    public function update(Request $request, $id)
    {
        $inquiry = SecurityInquiry::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,contacted,approved,rejected,deactivated',
            'admin_notes' => 'nullable|string',
            'contacted_at' => 'nullable|date',
        ]);

        $oldStatus = $inquiry->status;

        $data = [
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ];

        if ($request->contacted_at) {
            $data['contacted_at'] = $request->contacted_at;
        }

        if ($request->status === 'deactivated') {
            $data['is_active'] = false;
            $data['expires_at'] = null;

            try {
                Mail::to($inquiry->email)->send(new SecurityDeactivated($inquiry));
            } catch (\Exception $e) {
                Log::error('Deactivation email failed: ' . $e->getMessage());
            }
        }

        if ($request->status === 'approved' && !$inquiry->access_token) {
            $data['access_token'] = Str::random(32);
        }

        $inquiry->update($data);

        MonitoringService::info('security_status_changed', "Security inquiry #{$inquiry->id} status changed from {$oldStatus} to {$request->status}", [
            'inquiry_id' => $inquiry->id,
            'email' => $inquiry->email,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'admin_id' => auth()->id(),
        ]);

        return redirect()->route('admin.security.show', $inquiry->id)
            ->with('success', 'Security inquiry updated successfully!');
    }

    public function destroy($id)
    {
        $inquiry = SecurityInquiry::findOrFail($id);

        MonitoringService::warning('security_inquiry_deleted', 'Security inquiry #' . $inquiry->id . ' deleted by admin', [
            'inquiry_id' => $inquiry->id,
            'email' => $inquiry->email,
            'admin_id' => auth()->id(),
        ]);

        $inquiry->delete();

        return redirect()->route('admin.security.index')
            ->with('success', 'Security inquiry deleted successfully!');
    }

    public function regenerateToken($id)
    {
        $inquiry = SecurityInquiry::findOrFail($id);
        $inquiry->access_token = Str::random(32);
        $inquiry->save();

        MonitoringService::info('token_regenerated', 'Access token regenerated for inquiry #' . $inquiry->id, ['inquiry_id' => $inquiry->id, 'admin_id' => auth()->id()]);

        return redirect()->route('admin.security.show', $inquiry->id)
            ->with('success', 'New access token generated!');
    }

    public function resendLink($id)
    {
        $inquiry = SecurityInquiry::findOrFail($id);

        try {
            Mail::to($inquiry->email)->send(new SecurityInquiryConfirmation($inquiry));

            MonitoringService::info('status_link_resent', 'Status link resent to: ' . $inquiry->email, ['inquiry_id' => $inquiry->id, 'admin_id' => auth()->id()]);

            return redirect()->route('admin.security.show', $inquiry->id)
                ->with('success', 'Status link resent to client!');
        } catch (\Exception $e) {
            return redirect()->route('admin.security.show', $inquiry->id)
                ->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    public function activateClient(Request $request, $id)
    {
        $inquiry = SecurityInquiry::findOrFail($id);

        $request->validate([
            'expires_at' => 'required|date|after:now',
            'status' => 'nullable|string',
            'admin_notes' => 'nullable|string',
            'send_email' => 'nullable',
        ]);

        if (!$inquiry->access_token) {
            $inquiry->access_token = Str::random(32);
        }

        $expiresAt = \Carbon\Carbon::parse($request->expires_at);
        $durationDays = now()->diffInDays($expiresAt);

        $inquiry->duration = 'custom';
        $inquiry->duration_days = $durationDays;
        $inquiry->is_active = true;
        $inquiry->access_activated_at = now();
        $inquiry->status = $request->status ?? 'approved';
        $inquiry->admin_notes = $request->admin_notes;
        $inquiry->expires_at = $expiresAt;
        $inquiry->save();

        MonitoringService::info('admin_activated_client', 'Admin activated client access for ' . $inquiry->email, [
            'inquiry_id' => $inquiry->id,
            'email' => $inquiry->email,
            'duration_days' => $durationDays,
            'admin_id' => auth()->id(),
        ]);

        if ($request->send_email) {
            try {
                Mail::to($inquiry->email)->send(new SecurityAccessActivated($inquiry));
            } catch (\Exception $e) {
                Log::error('Activation email failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.security.show', $inquiry->id)
            ->with('success', 'Client access activated until ' . $expiresAt->format('M d, Y H:i') . '!');
    }

    public function deactivateClient($id)
    {
        $inquiry = SecurityInquiry::findOrFail($id);

        $inquiry->is_active = false;
        $inquiry->status = 'deactivated';
        $inquiry->expires_at = null;
        $inquiry->save();

        MonitoringService::warning('admin_deactivated_client', 'Admin deactivated client access for ' . $inquiry->email, [
            'inquiry_id' => $inquiry->id,
            'email' => $inquiry->email,
            'admin_id' => auth()->id(),
        ]);

        try {
            Mail::to($inquiry->email)->send(new SecurityDeactivated($inquiry));
        } catch (\Exception $e) {
            Log::error('Deactivation email failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.security.show', $inquiry->id)
            ->with('success', 'Client access deactivated successfully!');
    }

    public function updateDuration(Request $request, $id)
    {
        $inquiry = SecurityInquiry::findOrFail($id);

        $request->validate([
            'expires_at' => 'required|date',
            'status' => 'nullable|string',
            'admin_notes' => 'nullable|string',
            'send_email' => 'nullable',
        ]);

        $expiresAt = \Carbon\Carbon::parse($request->expires_at);
        $durationDays = now()->diffInDays($expiresAt);

        $inquiry->duration = 'custom';
        $inquiry->duration_days = $durationDays;
        $inquiry->status = $request->status ?? $inquiry->status;
        $inquiry->admin_notes = $request->admin_notes;
        $inquiry->expires_at = $expiresAt;
        $inquiry->save();

        MonitoringService::info('admin_updated_duration', 'Admin updated duration for ' . $inquiry->email, [
            'inquiry_id' => $inquiry->id,
            'email' => $inquiry->email,
            'duration_days' => $durationDays,
            'admin_id' => auth()->id(),
        ]);

        if ($request->send_email && $request->status === 'approved') {
            try {
                Mail::to($inquiry->email)->send(new SecurityAccessActivated($inquiry));
            } catch (\Exception $e) {
                Log::error('Email failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.security.show', $inquiry->id)
            ->with('success', 'Duration updated to ' . $durationDays . ' days! New expiry: ' . $expiresAt->format('M d, Y H:i'));
    }

    public function cancelSubscription($id)
    {
        $inquiry = SecurityInquiry::findOrFail($id);

        if (!$inquiry->stripe_subscription_id) {
            return back()->with('error', 'No active subscription found.');
        }

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            \Stripe\Subscription::update($inquiry->stripe_subscription_id, [
                'cancel_at_period_end' => true,
            ]);

            $inquiry->cancel_at_period_end = true;
            $inquiry->save();

            MonitoringService::warning('admin_cancelled_subscription', 'Admin cancelled subscription for ' . $inquiry->email, [
                'inquiry_id' => $inquiry->id,
                'email' => $inquiry->email,
                'subscription_id' => $inquiry->stripe_subscription_id,
                'admin_id' => auth()->id(),
            ]);

            return back()->with('success', 'Subscription will be cancelled at the end of the billing period.');
        } catch (\Exception $e) {
            Log::error('Admin cancel subscription error: ' . $e->getMessage());
            return back()->with('error', 'Failed to cancel subscription: ' . $e->getMessage());
        }
    }
}
