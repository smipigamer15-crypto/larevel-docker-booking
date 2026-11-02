@extends('layouts.admin')
@section('style-page', 'admin/users/edit')

@section('title', 'Edit User')

@section('content')
    <div class="admin-security py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-user-edit text-primary me-2"></i>Edit User
                    </h1>
                    <p class="text-secondary mb-0">Update user #{{ $user->id }}</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge-total">
                        <i class="fas fa-hashtag"></i>ID: {{ $user->id }}
                    </span>
                    <a href="{{ route('admin.users.index') }}" class="btn-clear">
                        <i class="fas fa-arrow-left"></i>Back
                    </a>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 flex-wrap">

                        @if($user->avatar && file_exists(public_path('storage/' . $user->avatar)))
                            <img src="{{ asset('storage/' . $user->avatar) }}"
                                class="rounded-circle"
                                width="64"
                                height="64"
                                alt="{{ $user->name }}"
                                data-view-style="admin-users-edit-1">
                        @else
                            <div class="avatar bg-primary-soft text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center"
                                    data-view-style="admin-users-edit-2">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                        @endif

                        <div class="flex-grow-1">
                            <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                            <p class="text-secondary mb-2">
                                <i class="fas fa-envelope me-1"></i>{{ $user->email }}
                            </p>
                            <span class="status-badge badge bg-{{ $user->isAdmin() ? 'purple' : 'primary' }}-soft text-{{ $user->isAdmin() ? 'purple' : 'primary' }} px-3 py-2 rounded-pill">
                                <i class="fas {{ $user->isAdmin() ? 'fa-crown' : 'fa-user' }} me-1"></i>
                                {{ $user->isAdmin() ? 'Admin' : 'User' }}
                            </span>
                        </div>

                        <div class="text-end d-none d-md-block">
                            <div class="small text-secondary text-uppercase fw-semibold" data-view-style="admin-users-edit-3">Joined</div>
                            <div class="fw-semibold">{{ $user->created_at->format('M d, Y') }}</div>
                            <small class="text-secondary">{{ $user->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Name</label>
                                <input type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    name="name"
                                    value="{{ old('name', $user->name) }}"
                                    placeholder="Enter full name"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    name="email"
                                    value="{{ old('email', $user->email) }}"
                                    placeholder="user@example.com"
                                    required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone</label>
                                <input type="text"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    name="phone"
                                    value="{{ old('phone', $user->phone) }}"
                                    placeholder="+1 (555) 000-0000">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Role</label>
                                <select class="form-select @error('role') is-invalid @enderror" name="role">
                                    <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>
                                        User
                                    </option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                        Admin
                                    </option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mt-4">
                                <div class="section-divider">
                                    <span><i class="fas fa-lock me-1"></i>Change Password</span>
                                </div>
                                <p class="text-secondary small mb-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Leave the password fields blank if you don't want to change it.
                                </p>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">New Password</label>
                                <div class="password-wrapper">
                                    <input type="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        name="password"
                                        id="password"
                                        placeholder="Enter new password">
                                    <button type="button" class="password-toggle" data-view-action="admin-users-edit-1" >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirm New Password</label>
                                <div class="password-wrapper">
                                    <input type="password"
                                        class="form-control"
                                        name="password_confirmation"
                                        id="password_confirmation"
                                        placeholder="Confirm new password">
                                    <button type="button" class="password-toggle" data-view-action="admin-users-edit-2" >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="submit" class="btn-primary-modern">
                                        <i class="fas fa-save me-2"></i>Update User
                                    </button>
                                    <a href="{{ route('admin.users.index') }}" class="btn-clear">
                                        <i class="fas fa-times"></i>Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <input type="hidden" data-page-script="admin/users/edit">
@endsection
