@extends('layouts.app')
@section('style-page', 'services/index')

@section('title', __('Our Services'))

@section('content')
    <section class="services-page py-5">
        <div class="container">
            <div class="services-header text-center mb-5">
                <h1 class="fw-bold section-title">{{ __('Explore Our Services') }}</h1>
                <p class="text-secondary" data-view-style="services-index-1">
                    {{ __('Find the perfect service for your needs') }}
                </p>
            </div>

            <div class="services-filters mb-5">
                <form method="GET" action="{{ route('services.index') }}" id="servicesFilterForm">
                    <div class="filters-wrapper">
                        <div class="row g-3">

                            <div class="col-lg-4 col-md-6">
                                <div class="filter-group">
                                    <label class="filter-label">
                                        <i class="fas fa-search me-2"></i>{{ __('Search') }}
                                    </label>
                                    <div class="search-box">
                                        <i class="fas fa-search search-icon"></i>
                                        <input type="text"
                                            name="search"
                                            class="form-control sf-field"
                                            placeholder="{{ __('Search services...') }}"
                                            value="{{ request('search') }}">
                                        @if(request('search'))
                                            <a href="{{ route('services.index') }}" class="search-clear">
                                                <i class="fas fa-times-circle"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-6 filter-category">
                                <div class="filter-group">
                                    <label class="filter-label">
                                        <i class="fas fa-tag me-2"></i>{{ __('Category') }}
                                    </label>
                                    <select name="category" class="form-select sf-field">
                                        <option value="">{{ __('All Categories') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-6 filter-sort">
                                <div class="filter-group">
                                    <label class="filter-label">
                                        <i class="fas fa-sort me-2"></i>{{ __('Sort By') }}
                                    </label>
                                    <select name="sort" class="form-select sf-field">
                                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>{{ __('Newest') }}</option>
                                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>{{ __('Most Popular') }}</option>
                                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>{{ __('Price: Low → High') }}</option>
                                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>{{ __('Price: High → Low') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-6 filter-action">
                                <div class="filter-group">
                                    <label class="filter-label">&nbsp;</label>
                                    <button type="submit" class="btn btn-apply-filter w-100">
                                        <i class="fas fa-search me-2"></i>{{ __('Apply') }}
                                    </button>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-6 filter-action">
                                <div class="filter-group">
                                    <label class="filter-label">&nbsp;</label>
                                    @if(request()->hasAny(['search', 'category', 'sort', 'min_price', 'max_price', 'duration']))
                                        <a href="{{ route('services.index') }}" class="btn btn-reset w-100">
                                            <i class="fas fa-undo-alt me-2"></i>{{ __('Reset') }}
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-reset w-100" disabled>
                                            <i class="fas fa-undo-alt me-2"></i>{{ __('Reset') }}
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-5 col-md-6">
                                <div class="filter-group">
                                    <label class="filter-label">
                                        <i class="fas fa-dollar-sign me-2"></i>{{ __('Price Range') }}
                                    </label>
                                    <div class="price-inputs">
                                        <div class="price-input-group">
                                            <span class="price-currency">$</span>
                                            <input type="number"
                                                name="min_price"
                                                class="form-control sf-field"
                                                placeholder="{{ __('Min') }}"
                                                value="{{ request('min_price') }}">
                                        </div>
                                        <span class="price-separator">—</span>
                                        <div class="price-input-group">
                                            <span class="price-currency">$</span>
                                            <input type="number"
                                                name="max_price"
                                                class="form-control sf-field"
                                                placeholder="{{ __('Max') }}"
                                                value="{{ request('max_price') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6">
                                <div class="filter-group">
                                    <label class="filter-label">
                                        <i class="fas fa-clock me-2"></i>{{ __('Duration') }}
                                    </label>
                                    <select name="duration" class="form-select sf-field">
                                        <option value="">{{ __('All Durations') }}</option>
                                        @foreach($durations as $duration)
                                            <option value="{{ $duration }}" {{ request('duration') == $duration ? 'selected' : '' }}>
                                                {{ $duration }} {{ __('min') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-12">
                                <div class="filter-group">
                                    <label class="filter-label">
                                        <i class="fas fa-chart-simple me-2"></i>{{ __('Results') }}
                                    </label>
                                    <div class="results-info">
                                        <span class="results-count">{{ $services->total() }}</span>
                                        <span class="results-text">{{ __('services found') }}</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div>

            <div class="services-grid">
                @if($services->count() > 0)
                    <div class="row g-4">
                        @foreach($services as $service)
                            <div class="col-sm-6 col-lg-4 col-xl-3">
                                <div class="service-card card-modern overflow-hidden">

                                    <div class="service-image-wrapper position-relative overflow-hidden">
                                        @if($service->image && file_exists(public_path($service->image)))
                                            <img src="{{ asset($service->image) }}" class="service-image" alt="{{ $service->title }}">
                                        @else
                                            <div class="service-image-placeholder">
                                                <i class="fas fa-calendar-check fa-3x text-primary" data-view-style="services-index-2"></i>
                                            </div>
                                        @endif

                                        <div class="service-overlay d-flex align-items-center justify-content-center">
                                            <a href="{{ route('services.show', $service) }}" class="btn btn-light rounded-pill px-4">
                                                <i class="fas fa-eye me-2"></i>{{ __('View Details') }}
                                            </a>
                                        </div>
                                    </div>

                                    <div class="service-content p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="fw-bold mb-0 service-title">{{ $service->title }}</h6>
                                            <span class="category-badge badge rounded-pill px-2 py-1">
                                                {{ $service->category->name ?? __('General') }}
                                            </span>
                                        </div>

                                        <p class="text-secondary small mb-2 service-description">
                                            {{ Str::limit($service->description, 60) }}
                                        </p>

                                        <div class="d-flex justify-content-between align-items-center service-meta">
                                            <div>
                                                <span class="fw-bold text-primary service-price">{{ number_format($service->price, 2) }} $</span>
                                                <span class="text-secondary small ms-1 service-duration">
                                                    <i class="far fa-clock me-1"></i>{{ $service->duration }} {{ __('min') }}
                                                </span>
                                            </div>
                                            <a href="{{ route('bookings.create', ['service_id' => $service->id]) }}" class="btn btn-primary btn-sm rounded-pill px-3 book-btn">
                                                {{ __('Book') }}
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="services-pagination mt-5 d-flex justify-content-center">
                        {{ $services->links() }}
                    </div>
                @else
                    <div class="empty-state text-center py-5">
                        <div class="empty-icon mb-4">
                            <i class="fas fa-search fa-4x text-muted" data-view-style="services-index-3"></i>
                        </div>
                        <h4 class="fw-bold">{{ __('No services found') }}</h4>
                        <p class="text-secondary">{{ __('Try adjusting your filters or search terms') }}</p>
                        <a href="{{ route('services.index') }}" class="btn btn-primary rounded-pill mt-3">
                            <i class="fas fa-undo me-2"></i>{{ __('Reset Filters') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>

@endsection
@push('styles')
    @vite('resources/css/services.css')
@endpush
