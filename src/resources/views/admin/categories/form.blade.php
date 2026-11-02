@extends('layouts.admin')
@section('title', $category->exists ? 'Edit Category' : 'Add Category')
@section('content')
    <div class="container py-4">
        <h1>{{ $category->exists ? 'Edit Category' : 'Add Category' }}</h1>
        <form method="POST" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="card card-body mt-4">
            @csrf
            @if($category->exists)
                @method('PUT')
            @endif
            <div class="mb-3">
                <label for="category-name" class="form-label">Name</label>
                <input id="category-name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $category->name) }}" maxlength="255" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="category-slug" class="form-label">Slug</label>
                <input id="category-slug" class="form-control @error('slug') is-invalid @enderror" name="slug" value="{{ old('slug', $category->slug) }}" maxlength="255" pattern="[a-z0-9]+(-[a-z0-9]+)*" required>
                <div class="form-text">Use lowercase letters, numbers and hyphens, for example wellness.</div>
                @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-primary" type="submit">Save category</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.categories.index') }}">Back</a>
            </div>
        </form>
    </div>
@endsection
