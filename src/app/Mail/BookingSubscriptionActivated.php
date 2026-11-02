<?php

namespace App\Mail;

use App\Models\BookingSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingSubscriptionActivated extends Mailable
{
    use Queueable, SerializesModels;

    public BookingSubscription $subscription;

    public function __construct(BookingSubscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your ' . $this->subscription->plan_label . ' Plan is Active');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.booking-subscription-activated');
    }
}
