<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraphs(3, true),
            'company' => fake()->company(),
            'location' => fake()->city().', '.fake()->stateAbbr(),
            'salary' => '$'.fake()->numberBetween(50, 200).'k - $'.fake()->numberBetween(100, 300).'k',
            'consultant_id' => User::factory(),
        ];
    }
}
