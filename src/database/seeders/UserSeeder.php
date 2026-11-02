<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        if (! app()->environment('local', 'testing')) {
            return;
        }

        foreach (config('demo.accounts', []) as $role => $account) {
            if (empty($account['email']) || empty($account['password'])) {
                continue;
            }

            if (! filter_var($account['email'], FILTER_VALIDATE_EMAIL) || strlen($account['password']) < 12) {
                throw new \InvalidArgumentException('Demo accounts require a valid email and a password of at least 12 characters.');
            }

            User::firstOrCreate(['email' => $account['email']], [
                'name' => $account['name'],
                'password' => Hash::make($account['password']),
                'role' => $role,
            ]);
        }
    }
}
