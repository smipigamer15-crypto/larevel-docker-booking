<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_two_factor_is_required_even_without_an_active_paid_plan(): void
    {
        Mail::fake();
        $user = User::factory()->create(['two_factor_enabled' => true]);

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('2fa.show'))
            ->assertSessionHas('2fa:user:id', $user->id);

        $this->assertGuest();
    }

    public function test_login_does_not_redirect_to_an_external_site(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'redirect' => 'https://example.org/phishing',
        ])->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_api_tokens_cannot_bypass_enabled_two_factor_authentication(): void
    {
        $user = User::factory()->create(['two_factor_enabled' => true]);

        $this->postJson('/api/v1/auth/token', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'test-device',
        ])->assertForbidden();

        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_read_only_profile_tokens_cannot_update_the_profile(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('read-only', ['profile:read']);

        $this->withToken($token->plainTextToken)
            ->putJson('/api/v1/profile', ['name' => 'Changed'])
            ->assertForbidden();

        $this->assertSame($user->name, $user->fresh()->name);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $hotFile = tempnam(sys_get_temp_dir(), 'booking-vite-');
        file_put_contents($hotFile, 'http://localhost:5173');
        $this->withVite();
        app(Vite::class)->useHotFile($hotFile);

        try {
            $response = $this->get('/login');

            $response->assertStatus(200)
                ->assertSee('data-style-page="auth/login"', false);

            $this->assertMatchesRegularExpression('/href="[^"]*\/auth(?:-[^"\/]+)?\.css"/', $response->getContent());
        } finally {
            unlink($hotFile);
        }
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
