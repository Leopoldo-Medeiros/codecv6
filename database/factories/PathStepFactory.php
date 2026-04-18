<?php

namespace Database\Factories;

use App\Models\Path;
use App\Models\PathStep;
use Illuminate\Database\Eloquent\Factories\Factory;

class PathStepFactory extends Factory
{
    protected $model = PathStep::class;

    public function definition(): array
    {
        return [
            'path_id'     => Path::factory(),
            'title'       => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'order'       => fake()->numberBetween(0, 100),
            'resources'   => null,
            'course_id'   => null,
        ];
    }
}
