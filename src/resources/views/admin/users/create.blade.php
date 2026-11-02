@extends('layouts.admin')
@section('title', 'Add User')
@section('content')
    <div class="container py-4">
        <h1>Add User</h1>
        <form method="POST" action="{{ route('admin.users.store') }}" class="card card-body mt-4">
            @csrf
            @foreach(['name' => 'Name', 'email' => 'Email', 'phone' => 'Phone', 'password' => 'Password', 'password_confirmation' => 'Confirm password'] as $field => $label)
                <div class="mb-3">
                    <label for="user-{{ $field }}" class="form-label">{{ $label }}</label>
                    <input id="user-{{ $field }}" name="{{ $field }}" class="form-control @error($field) is-invalid @enderror" type="{{ str_starts_with($field, 'password') ? 'password' : ($field === 'email' ? 'email' : ($field === 'phone' ? 'tel' : 'text')) }}" @unless(str_starts_with($field, 'password')) value="{{ old($field) }}" @endunless @if($field !== 'phone') required @endif @if(str_starts_with($field, 'password')) minlength="8" autocomplete="new-password" @else maxlength="255" @endif>
                    @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            @endforeach
            <div class="mb-3">
                <label for="user-role" class="form-label">Role</label>
                <select id="user-role" name="role" class="form-select" required>
                    <option value="user" @selected(old('role', 'user') === 'user')>User</option>
                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                </select>
                @error('role')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-primary" type="submit">Create user</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Back</a>
            </div>
        </form>
    </div>
@endsection
