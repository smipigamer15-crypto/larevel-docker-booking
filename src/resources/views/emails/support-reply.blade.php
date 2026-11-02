<!DOCTYPE html>
<html data-style-page="emails/support-reply">
    <head>

        {!! '<style>' . file_get_contents(resource_path('css/emails.css')) . '</style>' !!}
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>Support Reply</h2>
            </div>
            <div class="content">
                <p>Hello <strong>{{ $ticket->user->name }}</strong>,</p>
                <p>Our support team has replied to your ticket regarding: <strong>{{ $ticket->subject }}</strong></p>

                <div class="reply">
                    <h4 data-view-style="emails-support-reply-1">Our Response:</h4>
                    <p>{!! nl2br(e($replyMessage)) !!}</p>
                </div>

                <br>
                <p class="original">
                    <strong>Your original message:</strong><br>
                    {!! nl2br(e($ticket->message)) !!}
                </p>

                <p>Best regards,<br>{{ config('app.name') }} Support Team</p>
            </div>
        </div>
    </body>
</html>
