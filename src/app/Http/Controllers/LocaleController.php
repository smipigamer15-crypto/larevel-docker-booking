<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocaleController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', Rule::in(array_keys(config('localization.locales')))],
            'return_to' => ['nullable', 'string', 'max:2048'],
        ]);

        $returnTo = $validated['return_to'] ?? '/';

        if (!str_starts_with($returnTo, '/') || str_starts_with($returnTo, '//')
            || str_contains($returnTo, '\\') || preg_match('/[\x00-\x20]/', $returnTo)) {
            abort(422);
        }

        $request->session()->put('locale', $validated['locale']);

        return redirect()->to(url($returnTo))->withCookie(cookie(
            'locale',
            $validated['locale'],
            60 * 24 * 365,
            '/',
            null,
            $request->isSecure(),
            true,
            false,
            'lax',
        ));
    }
}
