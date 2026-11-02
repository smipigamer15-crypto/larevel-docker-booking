<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function envelope(): Envelope
    {
        $workspace = app(\App\Services\FeatureWorkspace::class);
        $settings = $workspace->settings($this->booking->user_id);
        $subject = $settings['email_body'] ? $workspace->render($settings['email_subject'], $this->booking) : 'Booking Confirmation #' . $this->booking->id;

        return new Envelope(subject: preg_replace('/[\r\n]+/', ' ', $subject));
    }

    public function content(): Content
    {
        $workspace = app(\App\Services\FeatureWorkspace::class);
        $body = $workspace->settings($this->booking->user_id)['email_body'];

        return new Content(view: $body ? 'emails.custom-booking-confirmation' : 'emails.booking-confirmation', with: ['customMessage' => $workspace->render($body, $this->booking)]);
    }
}
