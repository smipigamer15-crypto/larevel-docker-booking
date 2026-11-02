@extends('layouts.admin')
@section('style-page', 'admin/users/index')

@section('title', 'Manage Users')

@section('content')
    <div class="admin-security py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-users text-primary me-2"></i>Users
                    </h1>
                    <p class="text-secondary mb-0">Manage all users</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <a class="btn btn-primary" href="{{ route('admin.users.create') }}">Add User</a>
                    <span class="badge-total">
                        <i class="fas fa-users"></i>{{ $users->total() }} Total
                    </span>
                    <button class="btn-clear" data-view-action="admin-users-index-1" >
                        <i class="fas fa-sync-alt"></i>Refresh
                    </button>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-primary-soft mb-2">
                            <i class="fas fa-users text-primary"></i>
                        </div>
                        <h3 class="fw-bold text-primary mb-0">{{ $users->total() }}</h3>
                        <p class="small text-secondary mb-0">Total Users</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-purple-soft mb-2">
                            <i class="fas fa-crown text-purple"></i>
                        </div>
                        <h3 class="fw-bold text-purple mb-0">
                            {{ \App\Models\User::where('role', 'admin')->count() }}
                        </h3>
                        <p class="small text-secondary mb-0">Admins</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-success-soft mb-2">
                            <i class="fas fa-user-check text-success"></i>
                        </div>
                        <h3 class="fw-bold text-success mb-0">
                            {{ \App\Models\User::where('role', '!=', 'admin')->orWhereNull('role')->count() }}
                        </h3>
                        <p class="small text-secondary mb-0">Regular Users</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-info-soft mb-2">
                            <i class="fas fa-calendar-day text-info"></i>
                        </div>
                        <h3 class="fw-bold text-info mb-0">
                            {{ \App\Models\User::whereDate('created_at', today())->count() }}
                        </h3>
                        <p class="small text-secondary mb-0">Today</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 align-items-center">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search by name or email..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="role" class="form-select">
                                <option value="">All roles</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Regular User</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                        </div>
                        <div class="col-md-2">
                            @if(request()->hasAny(['search', 'role']))
                                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100 rounded-pill">
                                    <i class="fas fa-undo me-2"></i>Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 ps-4">#</th>
                                    <th class="py-3">Name</th>
                                    <th class="py-3">Email</th>
                                    <th class="py-3">Phone</th>
                                    <th class="py-3">Role</th>
                                    <th class="py-3">Joined</th>
                                    <th class="py-3 text-center pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td class="ps-4 fw-semibold">#{{ $user->id }}</td>

                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($user->avatar && file_exists(public_path('storage/' . $user->avatar)))
                                                    <img src="{{ asset('storage/' . $user->avatar) }}"
                                                        class="rounded-circle"
                                                        width="36"
                                                        height="36"
                                                        alt="{{ $user->name }}"
                                                        data-view-style="admin-users-index-1">
                                                @else
                                                    <div class="avatar bg-primary-soft text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center"
                                                            data-view-style="admin-users-index-2">
                                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-semibold">{{ $user->name }}</div>
                                                    @if($user->id === auth()->id())
                                                        <small class="text-primary fw-bold">You</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <a href="mailto:{{ $user->email }}" class="text-decoration-none text-dark">
                                                {{ $user->email }}
                                            </a>
                                        </td>

                                        <td>{{ $user->phone ?? '—' }}</td>

                                        <td>
                                            <span class="status-badge badge bg-{{ $user->isAdmin() ? 'purple' : 'primary' }}-soft text-{{ $user->isAdmin() ? 'purple' : 'primary' }} px-3 py-2 rounded-pill">
                                                <i class="fas {{ $user->isAdmin() ? 'fa-crown' : 'fa-user' }} me-1"></i>
                                                {{ $user->isAdmin() ? 'Admin' : 'User' }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-semibold small">{{ $user->created_at->format('M d, Y') }}</span>
                                                <small class="text-secondary">{{ $user->created_at->diffForHumans() }}</small>
                                            </div>
                                        </td>

                                        <td class="text-center pe-4">
                                            <div class="d-flex gap-1 justify-content-center align-items-center">
                                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                                        class="btn-action btn-action-edit"
                                                        title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                @if(!$user->isAdmin() && $user->id !== auth()->id())
                                                    <form action="{{ route('admin.users.destroy', $user->id) }}"
                                                            method="POST"
                                                            class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn-action btn-action-delete"
                                                                data-view-action="admin-users-index-2"
                                                                title="Delete">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="btn-action btn-action-disabled" title="Cannot delete admin">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="fas fa-users text-secondary display-4 d-block mb-3"></i>
                                            <h5 class="text-secondary">No users found</h5>
                                            <p class="text-secondary small">Try adjusting your search or filters</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($users->hasPages())
                <div class="pagination-wrapper">
                    {{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>
    </div>

@endsection
<input type="hidden" data-page-script="admin/users/index">
