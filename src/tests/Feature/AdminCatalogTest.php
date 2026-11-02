<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_can_be_created_edited_and_deleted_without_removing_services(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->post(route('admin.categories.store'), ['name' => 'Wellness', 'slug' => 'wellness'])
            ->assertRedirect(route('admin.categories.index'));
        $category = Category::sole();
        $this->get(route('admin.categories.edit', $category))->assertOk()->assertSee('Wellness');
        $this->put(route('admin.categories.update', $category), ['name' => 'Consultations', 'slug' => 'consultations'])
            ->assertSessionHasNoErrors();
        $service = Service::create([
            'category_id' => $category->id, 'title' => 'Consultation', 'description' => 'Consultation',
            'price' => 50, 'duration' => 30, 'is_active' => true,
        ]);
        $this->from(route('admin.categories.index'))->delete(route('admin.categories.destroy', $category))
            ->assertSessionHas('error');
        $this->assertModelExists($service);
        $service->delete();
        $this->delete(route('admin.categories.destroy', $category))->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseCount('categories', 0);
    }

    public function test_service_activation_respects_zero_and_inactive_services_are_hidden(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $category = Category::create(['name' => 'Wellness', 'slug' => 'wellness']);
        $payload = [
            'category_id' => $category->id, 'title' => 'Private consultation', 'description' => 'Consultation',
            'price' => 50, 'duration' => 30, 'is_active' => '0',
        ];
        $this->post(route('admin.services.store'), $payload)->assertSessionHasNoErrors();
        $service = Service::sole();
        $this->assertFalse($service->is_active);
        $this->get(route('services.show', $service))->assertNotFound();
        $this->get(route('services.index'))->assertDontSee('Private consultation');
        $this->put(route('admin.services.update', $service), array_merge($payload, ['is_active' => '1']))
            ->assertSessionHasNoErrors();
        $this->get(route('services.show', $service))->assertOk()->assertSee('Private consultation');
        $this->put(route('admin.services.update', $service), $payload)->assertSessionHasNoErrors();
        $this->assertFalse($service->fresh()->is_active);
    }

    public function test_admin_can_create_a_user_and_regular_users_cannot_manage_the_catalog(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->post(route('admin.users.store'), [
            'name' => 'New customer', 'email' => 'new-customer@example.com', 'role' => 'user',
            'password' => 'secure-password', 'password_confirmation' => 'secure-password',
        ])->assertRedirect(route('admin.users.index'));
        $user = User::where('email', 'new-customer@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('secure-password', $user->password));
        $this->actingAs($user)->post(route('admin.categories.store'), ['name' => 'Forbidden', 'slug' => 'forbidden'])
            ->assertRedirect(route('dashboard'));
        $this->assertDatabaseCount('categories', 0);
    }

    public function test_malformed_catalog_filters_return_validation_errors_instead_of_server_errors(): void
    {
        $this->getJson('/services?search[]=test')->assertUnprocessable()->assertJsonValidationErrors('search');
        $this->getJson('/services?min_price=-1')->assertUnprocessable()->assertJsonValidationErrors('min_price');
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        foreach (['users', 'services', 'bookings'] as $section) {
            $this->getJson('/admin/'.$section.'?search[]=test')->assertUnprocessable()->assertJsonValidationErrors('search');
        }
    }
}
