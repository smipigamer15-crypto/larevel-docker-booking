@props(['eyebrow', 'title', 'accent', 'description', 'icon' => 'fa-calendar-check', 'tone' => 'blue'])
<section class="feature-tool-hero marketing-hero" data-marketing-tone="{{ $tone }}">
    <div class="container"><div class="feature-hero-layout">
            <div class="feature-hero-copy">
                <span class="feature-category-pill"><i class="fas {{ $icon }}"></i>{{ __($eyebrow) }}</span>
                <h1>{{ __($title) }} <span>{{ __($accent) }}</span></h1>
                <p class="feature-hero-description">{{ __($description) }}</p>
                @isset($actions)<div class="feature-hero-actions">{{ $actions }}</div>@endisset
                @isset($notes)<div class="feature-hero-notes">{{ $notes }}</div>@endisset
            </div>
            <div class="marketing-hero-visual">
                @isset($visual)
                    {{ $visual }}
                @else
                    <div class="feature-hero-visual" aria-hidden="true"><div class="feature-visual-orbit"></div><div class="feature-visual-card">
                            <div class="feature-visual-heading"><span class="feature-visual-dots"><i></i><i></i><i></i></span><small>{{ __('BUILT AROUND YOU') }}</small></div>
                            <div class="feature-visual-symbol"><i class="fas {{ $icon }}"></i></div><strong>{{ __($eyebrow) }}</strong><p>{{ __('A simpler way to work, every day.') }}</p>
                            <div class="feature-visual-row"><span><i class="far fa-calendar-check"></i> {{ __('More time for what matters') }}</span><i class="fas fa-check text-success"></i></div>
                            <div class="feature-visual-row"><span><i class="fas fa-layer-group"></i> {{ __('Everything in one place') }}</span><i class="fas fa-check text-success"></i></div>
                    </div><span class="feature-visual-badge"><i class="fas fa-star"></i> {{ __('Your next chapter starts here') }}</span></div>
                @endisset
            </div>
    </div></div>
</section>
