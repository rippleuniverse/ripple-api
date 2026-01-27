<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AuthSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(10)->create();
        User::factory()->create([
            'full_name' => 'Admin admin',
            'email' => 'admin@rippleuniverse.org',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }
}
