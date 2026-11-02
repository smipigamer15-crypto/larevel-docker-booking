<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:10000',
        ]);
        
        $user = auth()->user();
        $subscription = $user->activeBookingSubscription();
        $isBusiness = $subscription && in_array($subscription->package, ['business', 'enterprise']);
        
        SupportTicket::create([
            'user_id' => $user->id,
            'subject' => $request->subject,
            'message' => $request->message,
            'is_priority' => $isBusiness,
            'status' => 'open',
        ]);
        
        return back()->with('success', 'Your support ticket has been created. ' . ($isBusiness ? 'Our priority team will contact you within 1 hour.' : 'We will reply shortly.'));
    }
}
