<?php

namespace App\Mail;

use App\Models\BookingSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingSubscriptionRenewed extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;
    public $amount;

    public function __construct(BookingSubscription $subscription, $amount)
    {
        $this->subscription = $subscription;
        $this->amount = $amount;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your ' . $this->subscription->plan_label . ' Plan has been renewed');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.booking-subscription-renewed');
    }
}
