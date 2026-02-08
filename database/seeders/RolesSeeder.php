<?php

namespace Database\Seeders;

use App\Models\OpenRole;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        OpenRole::factory(20)->create();
    }
}
