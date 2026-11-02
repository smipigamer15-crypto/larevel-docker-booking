@php
    $notices = [];
    foreach (['success' => __('All set'), 'error' => __('Something needs attention'), 'warning' => __('Please check'), 'info' => __('Good to know')] as $tone => $title) {
        $message = session($tone);
        if (is_string($message) && $message !== '') $notices[] = compact('tone', 'title', 'message');
    }
    $statusMessages = [
        'verification-link-sent' => __('Your verification email has been sent. Confirm your address to continue.'),
        'profile-updated' => __('Your account details have been updated.'),
        'avatar-removed' => __('Your profile photo has been removed.'),
        'password-updated' => __('Your password has been updated.'),
        'api-token-generated' => __('Your new API token is ready.'),
    ];
    if (isset($statusMessages[session('status')]) && !session('success')) {
        $notices[] = ['tone' => 'success', 'title' => __('All set'), 'message' => $statusMessages[session('status')]];
    } elseif (is_string(session('status')) && str_contains(session('status'), ' ') && !session('success')) {
        $notices[] = ['tone' => 'success', 'title' => __('All set'), 'message' => session('status')];
    }
    foreach (['default', 'updatePassword', 'userDeletion'] as $bag) {
        if ($errors->getBag($bag)->any()) {
            $notices[] = ['tone' => 'error', 'title' => __('Check your details'), 'message' => $errors->getBag($bag)->first()];
            break;
        }
    }
    $noticeIcons = ['success' => 'check', 'error' => 'exclamation', 'warning' => 'exclamation-triangle', 'info' => 'info'];
@endphp
<div id="site-notifications" class="site-notifications" role="region" aria-label="{{ __('Notifications') }}">
    @foreach(array_slice($notices, -3) as $notice)
        <article class="site-toast" data-toast data-tone="{{ $notice['tone'] }}" role="{{ $notice['tone'] === 'error' ? 'alert' : 'status' }}">
            <span class="site-toast-icon"><i class="fas fa-{{ $noticeIcons[$notice['tone']] }}" aria-hidden="true"></i></span>
        <div class="site-toast-copy"><strong>{{ __($notice['title']) }}</strong><p>{{ __($notice['message']) }}</p></div>
            <div class="site-toast-controls"><button type="button" data-toast-sound aria-label="{{ __('Notification sound') }}" title="{{ __('Notification sound') }}"><i class="fas fa-volume-high" aria-hidden="true"></i></button><button type="button" data-toast-dismiss aria-label="{{ __('Dismiss notification') }}"><i class="fas fa-xmark" aria-hidden="true"></i></button></div>
            <span class="site-toast-progress" aria-hidden="true"></span>
        </article>
    @endforeach
</div>
