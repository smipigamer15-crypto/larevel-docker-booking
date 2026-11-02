<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TwoFactorCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $code;
    public $expiresIn;

    public function __construct(User $user, $code, $expiresIn = 60)
    {
        $this->user = $user;
        $this->code = $code;
        $this->expiresIn = $expiresIn;
    }

    public function build()
    {
        return $this->subject('Your Two-Factor Authentication Code')
            ->view('emails.two-factor-code');
    }
}
