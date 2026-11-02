@extends('layouts.app')
@section('style-page', 'reviews/create')

@section('title', __('Write a Review'))

@section('content')
    <div class="review-page py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card-modern p-4 p-md-5" data-aos="fade-up">
                        <div class="text-center mb-4">
                            <div class="review-icon mb-3">
                                <i class="fas fa-star fa-3x text-warning"></i>
                            </div>
                            <h2 class="fw-bold">{{ __('Write a Review') }}</h2>
                            <p class="text-secondary">{{ __('Share your experience with') }} <strong>{{ $booking->service->title }}</strong></p>
                        </div>

                        <form method="POST" action="{{ route('reviews.store', $booking) }}">
                            @csrf

                            <div class="mb-4">
                                <label class="form-label fw-semibold">{{ __('Your Rating') }}</label>
                                <div class="rating-input d-flex gap-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <label class="rating-star" data-view-style="reviews-create-1">
                                            <input type="radio" name="rating" value="{{ $i }}" required data-view-style="reviews-create-2">
                                            <i class="fas fa-star" data-value="{{ $i }}"></i>
                                        </label>
                                    @endfor
                                </div>
                                @error('rating')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="comment" class="form-label fw-semibold">{{ __('Your Review') }}</label>
                                <textarea class="form-control form-input @error('comment') is-invalid @enderror" 
                                        id="comment" 
                                        name="comment" 
                                        rows="5" 
                                        placeholder="{{ __('Tell us about your experience...') }}" 
                                    required>{{ old('comment') }}</textarea>
                                @error('comment')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary rounded-pill px-5">
                                    <i class="fas fa-paper-plane me-2"></i>{{ __('Submit Review') }}
                                </button>
                                <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                    {{ __('Cancel') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" data-page-script="reviews/create">
@endsection
@push('styles')
    @vite('resources/css/reviews.css')
@endpush
