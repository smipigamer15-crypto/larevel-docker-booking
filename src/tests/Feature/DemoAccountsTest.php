<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoAccountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_no_demo_users_are_created_without_explicit_credentials(): void
    {
        config(['demo.accounts' => ['admin' => ['email' => null, 'password' => null]]]);
        $this->seed(UserSeeder::class);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_demo_accounts_hash_passwords_and_preserve_existing_accounts(): void
    {
        config(['demo.accounts' => ['admin' => [
            'name' => 'Demo Admin', 'email' => 'demo@example.com', 'password' => 'test-fixture-password',
        ]]]);
        $this->seed(UserSeeder::class);
        $user = User::sole();
        $this->assertTrue($user->isAdmin());
        $this->assertTrue(Hash::check('test-fixture-password', $user->password));
        $user->update(['name' => 'Existing Admin']);
        $this->seed(UserSeeder::class);
        $this->assertDatabaseCount('users', 1);
        $this->assertSame('Existing Admin', $user->fresh()->name);
    }

    public function test_demo_seeding_is_disabled_in_production(): void
    {
        config(['demo.accounts' => ['admin' => [
            'name' => 'Demo Admin', 'email' => 'demo@example.com', 'password' => 'test-fixture-password',
        ]]]);
        app()->instance('env', 'production');
        app(UserSeeder::class)->run();
        $this->assertDatabaseCount('users', 0);
    }
}
