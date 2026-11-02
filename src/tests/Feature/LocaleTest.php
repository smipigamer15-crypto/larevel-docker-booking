<?php

namespace Tests\Feature;

use App\Http\Middleware\ApplyCustomRules;
use App\Http\Middleware\FirewallMiddleware;
use App\Http\Middleware\MonitoringMiddleware;
use App\Http\Middleware\TrackPageVisit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([
            ApplyCustomRules::class,
            FirewallMiddleware::class,
            MonitoringMiddleware::class,
            TrackPageVisit::class,
        ]);

        Route::middleware('web')->get('/locale-probe', fn () => response()->json([
            'locale' => app()->getLocale(),
            'label' => __('My Bookings'),
        ]));

        Route::middleware('web')->get('/admin/locale-probe', fn () => response()->json([
            'locale' => app()->getLocale(),
            'time' => Carbon::now()->subWeeks(3)->diffForHumans(['parts' => 1]),
        ]));
    }

    public function test_default_language_is_english(): void
    {
        $this->get('/locale-probe')->assertOk()->assertJsonPath('locale', 'en')
            ->assertHeader('Content-Language', 'en');
    }

    public function test_all_languages_can_be_selected_and_persisted(): void
    {
        foreach (['en', 'uk', 'ru', 'pl', 'de'] as $locale) {
            $this->post('/language', ['locale' => $locale, 'return_to' => '/bookings/create?service_id=3'])
                ->assertRedirect('/bookings/create?service_id=3')
                ->assertSessionHas('locale', $locale)
                ->assertCookie('locale', $locale);

            $this->get('/locale-probe')->assertJsonPath('locale', $locale);
        }
    }

    public function test_cookie_language_is_restored_without_a_session_preference(): void
    {
        $this->withCookie('locale', 'pl')->get('/locale-probe')
            ->assertJsonPath('locale', 'pl')->assertJsonPath('label', 'Moje rezerwacje');
    }

    public function test_unknown_languages_are_rejected_and_invalid_preferences_fall_back_to_english(): void
    {
        $this->post('/language', ['locale' => '../../en'])->assertSessionHasErrors('locale');
        $this->withSession(['locale' => 'xx'])->get('/locale-probe')->assertJsonPath('locale', 'en');
    }

    public function test_language_switch_cannot_redirect_to_another_site(): void
    {
        foreach (['https://example.com', '//example.com', '/\\example.com'] as $destination) {
            $this->post('/language', ['locale' => 'uk', 'return_to' => $destination])->assertStatus(422);
        }
    }

    public function test_admin_dates_stay_english_after_selecting_ukrainian(): void
    {
        $this->withSession(['locale' => 'uk'])->get('/admin/locale-probe')
            ->assertOk()->assertJsonPath('locale', 'en')->assertJsonPath('time', '3 weeks ago');
    }
}
