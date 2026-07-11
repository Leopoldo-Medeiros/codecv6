<?php

namespace Database\Factories;

use App\Enums\ChallengeDifficulty;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ChallengeFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.$this->faker->unique()->numberBetween(1000, 9999),
            'description' => $this->faker->paragraph(),
            'difficulty' => $this->faker->randomElement(ChallengeDifficulty::values()),
            'boilerplate_code' => "<?php\n\nfunction solve() {\n    // TODO\n}\n",
            'tests_code' => "<?php\n\nclass SolveTest extends PHPUnit\\Framework\\TestCase\n{\n    public function test_it_works()\n    {\n        \$this->assertTrue(true);\n    }\n}\n",
            'is_premium' => false,
            'price_eur' => null,
            'created_by' => User::factory(),
        ];
    }

    public function premium(int $priceEur = 500): self
    {
        return $this->state(fn () => [
            'is_premium' => true,
            'price_eur' => $priceEur,
        ]);
    }
}
