<div class="profile-form-card card-modern p-4 p-md-5 mb-4">
    <div class="form-card-header">
        <h5 class="fw-bold mb-1">
            <i class="fas fa-user-edit text-primary me-2"></i>{{ __('Account Settings') }}
        </h5>
        <p class="text-secondary small mb-0">{{ __('Update your personal information') }}</p>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" class="profile-form" enctype="multipart/form-data" id="profileForm">
        @csrf
        @method('patch')

        <div class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label fw-semibold">{{ __('Full Name') }}</label>
                <div class="input-wrapper">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text"
                        class="form-control form-input @error('name') is-invalid @enderror"
                        id="name" autocomplete="name"
                        name="name"
                        value="{{ old('name', auth()->user()->name) }}"
                        required>
                </div>
                @error('name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label fw-semibold">{{ __('Email Address') }}</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email"
                        class="form-control form-input @error('email') is-invalid @enderror"
                        id="email" autocomplete="email"
                        name="email"
                        value="{{ old('email', auth()->user()->email) }}"
                        required>
                </div>
                @error('email')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="phone" class="form-label fw-semibold">{{ __('Phone Number') }}</label>
                <div class="input-wrapper">
                    <i class="fas fa-phone input-icon"></i>
                    <input type="text"
                        class="form-control form-input"
                        id="phone" autocomplete="tel"
                        name="phone"
                        value="{{ old('phone', auth()->user()->phone) }}">
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold">{{ __('Profile Photo') }}</label>

                <div class="d-flex gap-2 align-items-center">
                    <div class="input-wrapper flex-grow-1">
                        <i class="fas fa-image input-icon"></i>
                        <input type="file" class="form-control form-input" id="avatarInputForm" name="avatar" accept="image/*">
                    </div>

                    @if(auth()->user()->avatar)
                        <button type="button"
                                class="btn-remove-avatar"
                                title="{{ __('Remove photo') }}"
                                data-view-action="profile-edit-5" >
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    @endif
                </div>

                <small class="text-secondary d-block mt-1">{{ __('Max size: 2MB (JPG, PNG, GIF)') }}</small>
            </div>
        </div>

        <button type="submit" class="btn btn-primary rounded-pill px-5 mt-3">
            <i class="fas fa-save me-2"></i>{{ __('Update Profile') }}
        </button>
    </form>

    @if(auth()->user()->avatar)
        <form id="removeAvatarForm"
                method="POST"
                action="{{ route('profile.remove-avatar') }}"
                data-view-style="profile-edit-4">
            @csrf
            @method('DELETE')
        </form>
    @endif
</div>
