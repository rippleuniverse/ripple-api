<?php

namespace Database\Seeders;

use App\Models\EventCategory;
use App\Models\Podcast;
use App\Models\PodcastCategory;
use App\Models\ProductCategory;
use App\Models\ProgramCategory;
use Illuminate\Database\Seeder;

class ListingsSeeder extends Seeder
{
    public function run(): void
    {
        $programs = config('program_categories');
        $podcasts = config('podcast_categories');
        $productCategories = config('product_categories');
        ProgramCategory::factory()->createMany($programs);
        EventCategory::factory()->createMany($programs);
        PodcastCategory::factory()->createMany($podcasts);
        Podcast::factory()->count(10)->create();
        ProductCategory::factory()->createMany($productCategories);
    }
}
