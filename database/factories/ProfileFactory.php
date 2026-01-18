<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'birth_date' => fake()->dateTimeBetween('-55 years', '-21 years')->format('Y-m-d'),
            'profession' => fake()->randomElement([
                'Software Engineer',
                'Teacher',
                'Doctor',
                'Lawyer',
                'Designer',
                'Chef',
                'Architect',
                'Journalist',
                'Marketing Specialist',
                'Entrepreneur',
            ]),
        ];
    }
}
