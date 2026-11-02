<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use App\Models\BookingSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\SecurityInquiry;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        $securityInquiry = !$user->hasVerifiedEmail() ? null : SecurityInquiry::where("email", $user->email)
            ->latest()
            ->first();

        $bookingSubscription = !$user->hasVerifiedEmail() ? null : BookingSubscription::where("email", $user->email)
            ->latest()
            ->first();

        return view("profile.edit", [
            "user" => $user,
            "securityInquiry" => $securityInquiry,
            "bookingSubscription" => $bookingSubscription,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
            }

            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $request->session()->forget('verification_sent_at');
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function removeAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
            \Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return Redirect::route('profile.edit')
            ->with('status', 'avatar-removed');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag("userDeletion", [
            "password" => ["required", "current_password"],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to("/");
    }

    public function generateApiToken(Request $request): RedirectResponse
    {
        $user = $request->user();

        $subscription = $user->activeBookingSubscription();

        if (!$subscription || $subscription->package !== 'business') {
            return back()->with('error', 'API access is only available for Business plan.');
        }

        $user->update([
            'api_token' => Str::random(64),
        ]);

        return back()->with('status', 'api-token-generated');
    }
}
