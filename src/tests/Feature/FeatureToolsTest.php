<?php

namespace Tests\Feature;

use App\Models\SupportTicket;
use App\Models\User;
use App\Services\FeatureWorkspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class FeatureToolsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake('local');
        Http::preventStrayRequests();
        Mail::fake();
    }

    public function test_all_catalog_pages_render_and_soon_placeholders_are_removed(): void
    {
        $this->get('/features')->assertOk()->assertDontSee('Coming Soon')->assertDontSee('soon-card');
        foreach (config('feature_catalog') as $slug => $entry) {
            if ($slug === 'security') {
                continue;
            }
            $this->get(route($entry['category'] === 'resources' ? 'resources.show' : 'features.show', $slug))
                ->assertOk()->assertSee($entry['title']);
        }
    }

    public function test_settings_are_owned_by_the_signed_in_user_and_encrypted(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($user)->post(route('features.settings'), [
            'tool' => 'branding',
            'user_id' => $other->id,
            'business_name' => 'Private Brand',
            'welcome' => '<script>alert(1)</script>',
            'accent' => '#123456',
            'published' => '1',
        ])->assertSessionHasNoErrors();

        $workspace = app(FeatureWorkspace::class);
        $this->assertSame('Private Brand', $workspace->settings($user->id)['business_name']);
        $this->assertSame('Booking', $workspace->settings($other->id)['business_name']);
        $this->assertStringNotContainsString('Private Brand', Storage::disk('local')->get('feature-workspace/users/'.$user->id.'.json'));
        $this->get(route('features.booking-page', $user))->assertOk()
            ->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert(1)</script>', false);
        $this->get(route('features.booking-page', $other))->assertNotFound();
    }

    public function test_unverified_users_cannot_publish_or_connect_integrations(): void
    {
        $this->actingAs(User::factory()->unverified()->create())
            ->post(route('features.settings'), ['tool' => 'calendar'])
            ->assertRedirect(route('verification.notice'));
    }

    public function test_webhook_urls_cannot_target_local_or_arbitrary_servers(): void
    {
        $this->actingAs(User::factory()->create());
        foreach ([
            'http://127.0.0.1',
            'https://hooks.zapier.com.evil.test/hooks/catch/1/a',
            'https://hooks.zapier.com:8443/hooks/catch/1/a',
            'https://example.org/hook',
        ] as $url) {
            $this->postJson(route('features.settings'), ['tool' => 'zapier', 'zapier_url' => $url, 'zapier_enabled' => '1'])
                ->assertUnprocessable()->assertJsonValidationErrors('zapier_url');
        }
        Http::assertNothingSent();
    }

    public function test_calendar_link_reset_revokes_the_previous_url(): void
    {
        $user = User::factory()->create();
        $workspace = app(FeatureWorkspace::class);
        $this->actingAs($user)->post(route('features.settings'), ['tool' => 'calendar']);
        $url = URL::signedRoute('features.calendar.feed', ['user' => $user->id, 'token' => $workspace->settings($user->id)['calendar_token']]);
        $this->get($url)->assertOk()->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
        $this->post(route('features.settings'), ['tool' => 'calendar']);
        $this->get($url)->assertNotFound();
    }

    public function test_support_conversations_are_private_and_include_admin_replies(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $ticket = SupportTicket::create(['user_id' => $user->id, 'subject' => 'Question', 'message' => 'Hello', 'status' => 'open']);
        $this->actingAs($other)->getJson(route('features.support.messages', $ticket))->assertNotFound();
        $this->post(route('features.support.reply', $ticket), ['message' => 'Forged'])->assertNotFound();
        $this->actingAs($user)->post(route('features.support.reply', $ticket), ['message' => 'A follow-up'])->assertSessionHasNoErrors();
        $this->actingAs($admin)->post(route('admin.support.reply', $ticket), ['reply_message' => 'We can help.'])->assertRedirect()->assertSessionHasNoErrors();
        $this->actingAs($user)->getJson(route('features.support.messages', $ticket))
            ->assertOk()->assertJsonPath('messages.0.body', 'A follow-up')
            ->assertJsonPath('messages.1.body', 'We can help.')->assertJsonPath('messages.1.admin', true);
    }
}
