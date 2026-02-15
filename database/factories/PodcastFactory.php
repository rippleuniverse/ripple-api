<?php

namespace Database\Factories;

use App\Models\Podcast;
use App\Models\PodcastCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class PodcastFactory extends Factory
{
    protected $model = Podcast::class;

    public function definition(): array
    {
        return [
            'featured_image' => 'podcasts/featured-images/sample.png',
            'title' => $this->faker->word(),
            'description' => $this->faker->text(),
            'audio' => 'podcasts/featured-images/audio.mp3',
            'duration_in_minutes' => $this->faker->randomNumber(),
            'podcast_category_id' => PodcastCategory::inRandomOrder()->first()->id,
        ];
    }
}
