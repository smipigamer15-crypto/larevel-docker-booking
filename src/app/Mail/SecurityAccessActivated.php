<?php

namespace App\Mail;

use App\Models\SecurityInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SecurityAccessActivated extends Mailable
{
    use Queueable, SerializesModels;

    public $inquiry;

    public function __construct(SecurityInquiry $inquiry)
    {
        $this->inquiry = $inquiry;
    }

    public function build()
    {
        return $this->subject('Your Security Package is Now Active!')
            ->view('emails.security-activated');
    }
}
