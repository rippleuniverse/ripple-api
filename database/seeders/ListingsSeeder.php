<?php

namespace Database\Seeders;

use App\Models\EventCategory;
use App\Models\ProgramCategory;
use Illuminate\Database\Seeder;

class ListingsSeeder extends Seeder
{
    public function run(): void
    {
        $programs = config('program_categories');
        ProgramCategory::factory()->createMany($programs);
        EventCategory::factory()->createMany($programs);
    }
}
