<?php

namespace Database\Factories;

use App\Models\OpenRole;
use Illuminate\Database\Eloquent\Factories\Factory;

class OpenRoleFactory extends Factory
{
    protected $model = OpenRole::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'company_name' => $this->faker->company(),
            'company_location' => $this->faker->address(),
            'type' => $this->faker->randomElement(['full_time', 'part_time', 'internship', 'contract']),
            'experience_level' => $this->faker->randomElement(['beginner', 'intermediate', 'expert']),
            'style' => $this->faker->randomElement(['remote', 'on_site', 'hybrid']),
            'salary' => $this->faker->randomElement(['$30,000/month', '$10,000/month', '$15,000/month']),
            'description' => $this->faker->text(),
            'about_company' => $this->faker->text(),
            'responsibilities' => json_encode($this->faker->words()),
            'requirements' => json_encode($this->faker->words()),
            'benefits' => json_encode($this->faker->words()),
        ];
    }
}
