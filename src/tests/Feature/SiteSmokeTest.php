<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SiteSmokeTest extends TestCase
{
    use RefreshDatabase;

    public static function adminPages(): array
    {
        return array_map(fn ($path) => [$path], [
            'dashboard', 'bookings', 'services', 'services/create', 'categories', 'categories/create',
            'users', 'users/create', 'calendar', 'subscriptions', 'security', 'monitoring',
            'firewall', 'firewall/logs', 'vulnerability', 'backups', 'api-tokens', 'custom-rules', 'sms-logs', 'support',
        ]);
    }

    #[DataProvider('adminPages')]
    public function test_admin_sections_render_in_english(string $path): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->withSession(['locale' => 'ru'])->get('/admin/'.$path)
            ->assertOk()->assertHeader('Content-Language', 'en')
            ->assertSee('data-admin', false);
    }

    public function test_public_pages_render_in_all_languages(): void
    {
        foreach (['en', 'uk', 'ru', 'pl', 'de'] as $locale) {
            foreach (['/', '/services', '/pricing', '/enterprise', '/features/security', '/features', '/login', '/register', '/forgot-password'] as $path) {
                $this->withSession(['locale' => $locale])->get($path)
                    ->assertOk()->assertHeader('Content-Language', $locale);
            }
        }
    }
}
