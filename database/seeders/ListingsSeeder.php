<?php

namespace Database\Seeders;

use App\Models\PodcastCategory;
use Illuminate\Database\Seeder;

class ListingsSeeder extends Seeder
{
    public function run(): void
    {
//        $programs = config('program_categories');
        $podcasts = config('podcast_categories');
//        $productCategories = config('product_categories');
//        ProgramCategory::factory()->createMany($programs);
//        EventCategory::factory()->createMany($programs);
//        Podcast::factory()->count(10)->create();
//        ProductCategory::factory()->createMany($productCategories);
        PodcastCategory::factory()->createMany($podcasts);
    }
}
