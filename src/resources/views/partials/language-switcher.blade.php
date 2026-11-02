<form action="{{ route('locale.update') }}" method="POST" class="language-switcher">
    @csrf
    <input type="hidden" name="return_to" value="{{ request()->getRequestUri() }}">
    <label class="visually-hidden" for="site-language-{{ $placement ?? 'nav' }}">{{ __('Language') }}</label>
    <i class="fas fa-globe" aria-hidden="true"></i>
    <select name="locale" id="site-language-{{ $placement ?? 'nav' }}" class="form-select" data-language-select>
        @foreach(config('localization.locales') as $locale => $name)
            <option value="{{ $locale }}" @selected(app()->getLocale() === $locale) lang="{{ $locale }}">{{ $name }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-outline-secondary" aria-label="{{ __('Change language') }}">
        <i class="fas fa-check" aria-hidden="true"></i>
    </button>
</form>
