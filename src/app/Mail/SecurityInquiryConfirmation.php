<?php

namespace App\Mail;

use App\Models\SecurityInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SecurityInquiryConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $inquiry;

    public function __construct(SecurityInquiry $inquiry)
    {
        $this->inquiry = $inquiry;
    }

    public function build()
    {
        return $this->subject('Security Package Request Confirmation')
            ->view('emails.security-confirmation');
    }
}
