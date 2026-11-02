@extends('layouts.admin')
@section('style-page', 'admin/services/create')

@section('title', 'Create Service')

@section('content')
    <div class="admin-security py-4">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="fw-bold mb-1">
                        <i class="fas fa-plus-circle text-primary me-2"></i>Create Service
                    </h1>
                    <p class="text-secondary mb-0">Add a new service to your catalog</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge-total">
                        <i class="fas fa-plus"></i>New
                    </span>
                    <a href="{{ route('admin.services.index') }}" class="btn-clear">
                        <i class="fas fa-arrow-left"></i>Back
                    </a>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="avatar-lg bg-primary-soft text-primary fw-bold rounded-circle d-flex align-items-center justify-content-center"
                                data-view-style="admin-services-create-1">
                            <i class="fas fa-concierge-bell"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h4 class="fw-bold mb-1">New Service</h4>
                            <p class="text-secondary mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                Fill in the fields below to create a new service
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Service Title</label>
                                <input type="text"
                                    class="form-control @error('title') is-invalid @enderror"
                                    name="title"
                                    value="{{ old('title') }}"
                                    placeholder="Enter service title"
                                    required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" name="category_id">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Price ($)</label>
                                <input type="number"
                                    step="0.01"
                                    min="0"
                                    class="form-control @error('price') is-invalid @enderror"
                                    name="price"
                                    value="{{ old('price') }}"
                                    placeholder="0.00"
                                    required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Duration (minutes)</label>
                                <input type="number"
                                    min="1"
                                    class="form-control @error('duration') is-invalid @enderror"
                                    name="duration"
                                    value="{{ old('duration') }}"
                                    placeholder="60"
                                    required>
                                @error('duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                        name="description"
                                        rows="4"
                                    placeholder="Describe the service...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Service Image</label>

                                <input type="file"
                                    name="image"
                                    class="form-control @error('image') is-invalid @enderror"
                                    accept="image/*">

                                <small class="text-secondary">Max 2MB · JPG, PNG</small>

                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select class="form-select @error('is_active') is-invalid @enderror" name="is_active">
                                    <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mt-4">
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="submit" class="btn-primary-modern">
                                        <i class="fas fa-save me-2"></i>Create Service
                                    </button>
                                    <a href="{{ route('admin.services.index') }}" class="btn-clear">
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

@endsection
