<!DOCTYPE html>
<html data-style-page="emails/verify-email" lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Confirm your email</title>
        {!! '<style>' . file_get_contents(resource_path('css/emails.css')) . '</style>' !!}
    </head><body><table role="presentation" class="verification-mail-shell" width="100%" cellspacing="0" cellpadding="0"><tr><td align="center">
                    <table role="presentation" class="verification-mail-card" width="560" cellspacing="0" cellpadding="0"><tr><td>
                                <div class="verification-mail-brand">B<span>Booking</span></div>
                                <span class="verification-mail-label">ONE SMALL STEP</span><h1>A warm welcome.<br>Let's make it official.</h1>
                                <p>Hello {{ $name }},</p><p>Confirm your email address to connect your account, manage your plans and make the most of your booking tools.</p>
                                <p class="verification-mail-action"><a class="verification-mail-button" href="{{ $verificationUrl }}">Confirm my email address</a></p>
                                <p class="verification-mail-note">This secure link expires in {{ $expiresIn }} minutes. If you didn't create this account, you can ignore this email.</p>
                                <div class="verification-mail-divider"></div><p class="verification-mail-note">Button not working? Copy this link into your browser:</p><p class="verification-mail-link"><a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a></p>
                    </td></tr></table><p class="verification-mail-footer">Booking · A simpler way to plan your day.</p>
</td></tr></table></body></html>
