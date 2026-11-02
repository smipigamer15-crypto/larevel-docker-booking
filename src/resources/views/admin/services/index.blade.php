@extends('layouts.admin')
@section('style-page', 'admin/services/index')

@section('title', 'Manage Services')

@section('content')
    <div class="admin-security py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-concierge-bell text-primary me-2"></i>Services
                    </h1>
                    <p class="text-secondary mb-0">Manage all services</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge-total">
                        <i class="fas fa-concierge-bell"></i>{{ $services->total() }} Total
                    </span>
                    <a href="{{ route('admin.services.create') }}" class="btn-clear">
                        <i class="fas fa-plus"></i>Add Service
                    </a>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-primary-soft mb-2">
                            <i class="fas fa-concierge-bell text-primary"></i>
                        </div>
                        <h3 class="fw-bold text-primary mb-0">{{ $services->total() }}</h3>
                        <p class="small text-secondary mb-0">Total Services</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-success-soft mb-2">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                        <h3 class="fw-bold text-success mb-0">
                            {{ \App\Models\Service::where('is_active', true)->count() }}
                        </h3>
                        <p class="small text-secondary mb-0">Active</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-warning-soft mb-2">
                            <i class="fas fa-pause-circle text-warning"></i>
                        </div>
                        <h3 class="fw-bold text-warning mb-0">
                            {{ \App\Models\Service::where('is_active', false)->count() }}
                        </h3>
                        <p class="small text-secondary mb-0">Inactive</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="stat-card bg-white p-3 rounded-4 shadow-sm text-center">
                        <div class="stat-icon bg-purple-soft mb-2">
                            <i class="fas fa-dollar-sign text-purple"></i>
                        </div>
                        <h3 class="fw-bold text-purple mb-0">
                            {{ number_format(\App\Models\Service::sum('price'), 0) }} $
                        </h3>
                        <p class="small text-secondary mb-0">Total Value</p>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.services.index') }}" class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control"
                                placeholder="Search service name..."
                                value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="category_id" class="form-select">
                                <option value="">All categories</option>
                                @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All statuses</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100 rounded-pill">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
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
                                    <th class="py-3">Title</th>
                                    <th class="py-3">Category</th>
                                    <th class="py-3">Price</th>
                                    <th class="py-3">Duration</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3 text-center pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $service)
                                    <tr>
                                        <td class="ps-4 fw-semibold">#{{ $service->id }}</td>

                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar bg-primary-soft text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center"
                                                        data-view-style="admin-services-index-1">
                                                    <i class="fas fa-concierge-bell"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $service->title }}</div>
                                                    @if($service->description)
                                                        <small class="text-secondary">{{ Str::limit($service->description, 40) }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <span class="badge bg-light text-dark px-3 py-2 rounded-pill">
                                                <i class="fas fa-folder me-1"></i>
                                                {{ $service->category->name ?? 'General' }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="fw-bold text-primary">{{ number_format($service->price, 2) }} $</span>
                                        </td>

                                        <td>
                                            <span class="text-secondary">
                                                <i class="fas fa-clock me-1"></i>{{ $service->duration }} min
                                            </span>
                                        </td>

                                        <td>
                                            <span class="status-badge badge bg-{{ $service->is_active ? 'success' : 'danger' }}-soft text-{{ $service->is_active ? 'success' : 'danger' }} px-3 py-2 rounded-pill">
                                                <i class="fas {{ $service->is_active ? 'fa-check-circle' : 'fa-pause-circle' }} me-1"></i>
                                                {{ $service->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>

                                        <td class="text-center pe-4">
                                            <div class="d-flex gap-1 justify-content-center align-items-center">
                                                <a href="{{ route('admin.services.edit', $service->id) }}"
                                                        class="btn-action btn-action-edit"
                                                        title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.services.destroy', $service->id) }}"
                                                        method="POST"
                                                        class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="btn-action btn-action-delete"
                                                            data-view-action="admin-services-index-1"
                                                            title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="fas fa-concierge-bell text-secondary display-4 d-block mb-3"></i>
                                            <h5 class="text-secondary">No services found</h5>
                                            <p class="text-secondary small">Try adjusting your filters or add a new service</p>
                                            <a href="{{ route('admin.services.create') }}" class="btn-clear mt-2">
                                                <i class="fas fa-plus"></i>Add Service
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($services->hasPages())
                <div class="pagination-wrapper">
                    {{ $services->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>
    </div>

@endsection
<input type="hidden" data-page-script="admin/services/index">
