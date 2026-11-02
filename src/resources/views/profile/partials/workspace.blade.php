@php
    $workspaceTools = [
        'customization' => [__('Your booking page'), 'palette'],
        'calendar-sync' => [__('Calendar connection'), 'calendar-alt'],
        'emails' => [__('Email templates'), 'envelope'],
        'zapier' => [__('Automations'), 'bolt'],
    ];
@endphp

<div class="profile-tools-grid mb-4">
    @foreach($workspaceTools as $slug => [$label, $icon])
        <a href="{{ route('features.show', $slug) }}" class="profile-tool-link">
            <span><i class="fas fa-{{ $icon }}"></i></span>
            <strong>{{ $label }}</strong>
            <i class="fas fa-arrow-up-right-from-square"></i>
        </a>
    @endforeach
</div>
<div class="profile-form-card card-modern p-4 p-md-5 mb-4">
    <div class="form-card-header">
        <h5 class="fw-bold mb-1">
            <i class="fas fa-code text-primary me-2"></i>{{ __('Developer & Support') }}
        </h5>
        <p class="text-secondary small mb-0">{{ __('API access and priority support for Business users') }}</p>
    </div>

    @php
        $isBusiness = $bookingSubscription && $bookingSubscription->is_active && ($bookingSubscription->package === 'business' || $bookingSubscription->package === 'enterprise');
    @endphp

    @if($isBusiness)
        <div class="row g-4">
            <div class="col-md-6">
                <div class="p-3 bg-light rounded-4 h-100">
                    <h6 class="fw-bold mb-3 text-secondary"><i class="fas fa-key me-2"></i>{{ __('API Access') }}</h6>

                    @if(auth()->user()->api_token)
                        <p class="text-success small mb-2"><i class="fas fa-check-circle me-1"></i>{{ __('Your API token is active.') }}</p>
                        <div class="input-group mb-2">
                            <input type="password" class="form-control form-control-sm" value="{{ auth()->user()->api_token }}" readonly id="apiToken" aria-label="{{ __('Your API token') }}">
                            <button class="btn btn-outline-primary btn-sm" data-view-action="profile-edit-6"  type="button">
                                <i class="fas fa-copy"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-reveal-target="apiToken" aria-label="{{ __('Show API token') }}" aria-pressed="false"><i class="fas fa-eye"></i></button>
                        </div>
                        <form method="POST" action="{{ route('profile.generate-api-token') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary w-100" data-view-action="profile-edit-7" >
                                {{ __('Regenerate Token') }}
                            </button>
                        </form>
                    @else
                        <p class="text-secondary small mb-3">{{ __('Generate an API key to integrate our booking system with your own applications.') }}</p>
                        <form method="POST" action="{{ route('profile.generate-api-token') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                <i class="fas fa-plus me-1"></i>{{ __('Generate API Token') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="col-md-6">
                <div class="p-3 rounded-4 h-100" data-view-style="profile-edit-5">
                    <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-bolt me-2"></i>{{ __('Priority Support') }}</h6>
                    <p class="text-secondary small mb-3">
                        {{ __('As a Business user, you get top-priority support. Submit a ticket below.') }}
                    </p>

                    <form method="POST" action="{{ route('support.ticket.store') }}">
                        @csrf
                        <div class="mb-2">
                            <input type="text" name="subject" class="form-control form-control-sm" placeholder="{{ __('Subject (e.g., API Error)') }}" required>
                        </div>
                        <div class="mb-2">
                            <textarea name="message" class="form-control form-control-sm" rows="2" placeholder="{{ __('Describe your issue...') }}" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary w-100" data-view-style="profile-edit-6">
                            <i class="fas fa-paper-plane me-1"></i>{{ __('Submit Ticket') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="card border-0 rounded-4 p-4 text-center" data-view-style="profile-edit-7">
            <div class="mb-3">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm" data-view-style="profile-edit-8">
                    <i class="fas fa-lock"></i>
                </div>
            </div>
            <h5 class="fw-bold mb-2">{{ __('Business Features Locked') }}</h5>
            <p class="text-secondary small mb-4 mx-auto" data-view-style="profile-edit-9">
                {{ __('API access and Priority Support are exclusive to the Business plan. Upgrade to unlock them.') }}
            </p>
            <div>
                <a href="{{ route('pricing') }}" class="btn btn-primary rounded-pill px-4 py-2">
                    <i class="fas fa-arrow-up me-2"></i>{{ __('Upgrade to Business') }}
                </a>
            </div>
        </div>
    @endif
</div>
