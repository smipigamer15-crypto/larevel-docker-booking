<?php

namespace App\Mail;

use App\Models\SecurityInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SecurityInquiryAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $inquiry;

    public function __construct(SecurityInquiry $inquiry)
    {
        $this->inquiry = $inquiry;
    }

    public function build()
    {
        return $this->subject('New Security Inquiry')
            ->view('emails.admin-security-notification');
    }
}
