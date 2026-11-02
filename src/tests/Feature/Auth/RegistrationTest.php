<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_cannot_set_privileges_or_verify_an_email(): void
    {
        \Illuminate\Support\Facades\Mail::fake();
        \Illuminate\Support\Facades\Notification::fake();

        $this->post('/register', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
            'plan' => 'business',
            'email_verified_at' => now()->toDateTimeString(),
        ])->assertRedirect(route('verification.notice', absolute: false));

        $user = \App\Models\User::where('email', 'new@example.com')->firstOrFail();
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->hasVerifiedEmail());
        $this->assertFalse($user->hasActiveBookingSubscription());
        \Illuminate\Support\Facades\Notification::assertSentTo($user, \Illuminate\Auth\Notifications\VerifyEmail::class);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        \Illuminate\Support\Facades\Notification::fake();
        \Illuminate\Support\Facades\Mail::fake();
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('verification.notice', absolute: false));
        $response->assertSessionHas('verification_sent_at');
    }

    public function test_registration_stays_usable_when_mail_delivery_fails(): void
    {
        \Illuminate\Support\Facades\Mail::fake();
        \Illuminate\Support\Facades\Notification::shouldReceive('send')->once()
            ->andThrow(new \RuntimeException('SMTP unavailable'));
        $this->post('/register', [
            'name' => 'New User',
            'email' => 'mail-test@example.test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('verification.notice'))->assertSessionHas('error');
        $this->assertAuthenticated();
    }
}
