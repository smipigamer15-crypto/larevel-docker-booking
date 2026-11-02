<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SupportTicketReplyMail;

class SupportTicketController extends Controller
{
    public function messages(SupportTicket $ticket, \App\Services\FeatureWorkspace $workspace)
    {
        return response()->json(['messages' => $workspace->messages($ticket->id)])
            ->header('Cache-Control', 'private, no-store');
    }

    public function index()
    {
        $tickets = SupportTicket::with('user')
            ->orderBy('is_priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $threads = $tickets->getCollection()->mapWithKeys(fn($ticket) => [
            $ticket->id => app(\App\Services\FeatureWorkspace::class)->messages($ticket->id),
        ]);

        return view('admin.support.index', compact('tickets', 'threads'));
    }

    public function resolve($id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->update(['status' => 'resolved']);
        return back()->with('success', 'Ticket marked as resolved.');
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply_message' => 'required|string|max:5000',
        ]);

        $ticket = SupportTicket::with('user')->findOrFail($id);

        app(\App\Services\FeatureWorkspace::class)->addMessage($ticket->id, $request->reply_message, true);
        try {
            Mail::to($ticket->user->email)->send(new SupportTicketReplyMail($ticket, $request->reply_message));
        } catch (\Throwable $exception) {
            return back()->with('success', 'Reply saved in the conversation. Email notification could not be delivered.');
        }

        return back()->with('success', 'Reply sent successfully to ' . $ticket->user->email);
    }
}
