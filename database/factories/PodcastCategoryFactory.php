<?php

namespace Database\Factories;

use App\Models\PodcastCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class PodcastCategoryFactory extends Factory
{
    protected $model = PodcastCategory::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'slug' => $this->faker->unique()->slug(),
        ];
    }
}
