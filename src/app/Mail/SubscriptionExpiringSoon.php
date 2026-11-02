<?php

namespace App\Mail;

use App\Models\BookingSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionExpiringSoon extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;

    public function __construct(BookingSubscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your ' . $this->subscription->plan_label . ' Subscription Ends in 3 Days');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.subscription-expiring-soon');
    }
}
