<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\SupportTicket;

class SupportTicketReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $replyMessage;

    public function __construct(SupportTicket $ticket, $replyMessage)
    {
        $this->ticket = $ticket;
        $this->replyMessage = $replyMessage;
    }

    public function build()
    {
        return $this->subject('Reply to your ticket: ' . $this->ticket->subject)
            ->view('emails.support-reply');
    }
}
