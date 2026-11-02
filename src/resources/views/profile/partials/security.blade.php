<div class="profile-form-card card-modern p-4 p-md-5 mb-4">
    <div class="form-card-header">
        <h5 class="fw-bold mb-1">
            <i class="fas fa-shield-alt text-primary me-2"></i>{{ __('Two-Factor Authentication') }}
        </h5>
        <p class="text-secondary small mb-0">{{ __('Add an extra layer of security to your account') }}</p>
    </div>

    @php
        $user = auth()->user();
        $canUse2FA = $user->canUseTwoFactor();
    @endphp

    @if($canUse2FA)
        @if($user->two_factor_enabled)
            <div class="alert alert-success rounded-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fs-4 me-3"></i>
                    <div>
                        <strong>{{ __('2FA is enabled') }}</strong>
                        <p class="mb-0 text-secondary small">
                            {{ __('You\'ll receive a code via email each time you log in.') }}
                        </p>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('2fa.disable') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger rounded-pill px-4" data-view-action="profile-edit-8" >
                    <i class="fas fa-unlink me-2"></i>{{ __('Disable 2FA') }}
                </button>
            </form>
        @else
            <div class="alert alert-info rounded-3">
                <i class="fas fa-info-circle me-2"></i>
                {{ __('2FA adds extra security. Each login will require a code sent to your email.') }}
            </div>
            <form method="POST" action="{{ route('2fa.enable') }}">
                @csrf
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-shield-alt me-2"></i>{{ __('Enable 2FA') }}
                </button>
            </form>
        @endif
    @else
        <div class="card border-0 rounded-4 p-4 text-center" data-view-style="profile-edit-10">
            <div class="mb-3">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm" data-view-style="profile-edit-11">
                    <i class="fas fa-lock"></i>
                </div>
            </div>
            <h5 class="fw-bold mb-2">{{ __('2FA is Locked') }}</h5>
            <p class="text-secondary small mb-4 mx-auto" data-view-style="profile-edit-12">
                {{ __('Two-factor authentication is a premium feature. Upgrade your plan to protect your account with an extra layer of security.') }}
            </p>
            <div>
                <a href="{{ route('security') }}" class="btn btn-primary rounded-pill px-4 py-2">
                    <i class="fas fa-arrow-up me-2"></i>{{ __('Upgrade Plan') }}
                </a>
            </div>
        </div>
    @endif
</div>

<div class="profile-form-card card-modern p-4 p-md-5 mb-4">
    <div class="form-card-header">
        <h5 class="fw-bold mb-1">
            <i class="fas fa-lock text-primary me-2"></i>{{ __('Password') }}
        </h5>
        <p class="text-secondary small mb-0">{{ __('Change your password') }}</p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="profile-form">
        @csrf
        @method('put')

        <div class="row g-3">
            <div class="col-md-6">
                <label for="current_password" class="form-label fw-semibold">{{ __('Current Password') }}</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password"
                        class="form-control form-input @error('current_password', 'updatePassword') is-invalid @enderror"
                        id="current_password" autocomplete="current-password"
                        name="current_password"
                        required>
                    <button type="button" class="password-toggle" data-view-action="profile-edit-10" >
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('current_password', 'updatePassword')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="password" class="form-label fw-semibold">{{ __('New Password') }}</label>
                <div class="input-wrapper">
                    <i class="fas fa-key input-icon"></i>
                    <input type="password"
                        class="form-control form-input @error('password', 'updatePassword') is-invalid @enderror"
                        id="password" autocomplete="new-password"
                        name="password"
                        required>
                    <button type="button" class="password-toggle" data-view-action="profile-edit-11" >
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password', 'updatePassword')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="password_confirmation" class="form-label fw-semibold">{{ __('Confirm Password') }}</label>
                <div class="input-wrapper">
                    <i class="fas fa-check-circle input-icon"></i>
                    <input type="password"
                        class="form-control form-input"
                        id="password_confirmation" autocomplete="new-password"
                        name="password_confirmation"
                        required>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary rounded-pill px-5 mt-3">
            <i class="fas fa-key me-2"></i>{{ __('Update Password') }}
        </button>
    </form>
</div>

<details class="profile-danger-zone" @if($errors->getBag('userDeletion')->any()) open @endif><summary><i class="fas fa-trash-alt"></i> {{ __('Delete your account') }}</summary>
    <div class="profile-form-card card-modern p-4 p-md-5 border-danger">
        <div class="form-card-header">
            <h5 class="fw-bold mb-1 text-danger">
                <i class="fas fa-trash-alt text-danger me-2"></i>{{ __('Delete Account') }}
            </h5>
            <p class="text-secondary small mb-0">{{ __('This action cannot be undone') }}</p>
        </div>

        <form method="POST" action="{{ route('profile.destroy') }}" class="profile-form">
            @csrf
            @method('delete')

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="delete_password" class="form-label fw-semibold">{{ __('Confirm Password') }}</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password"
                            class="form-control form-input"
                            id="delete_password"
                            name="password"
                            required>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-outline-danger rounded-pill px-5 mt-3" data-view-action="profile-edit-12" >
                <i class="fas fa-trash-alt me-2"></i>{{ __('Delete Account') }}
            </button>
        </form>
    </div>
</details>
