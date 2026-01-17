<?php

namespace Database\Factories;

use App\Models\Path;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PathFactory extends Factory
{
    protected $model = Path::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'consultant_id' => User::factory(),
        ];
    }
}
