<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgesSeeder extends Seeder
{
    /**
     * The 3 F1 milestone badges (see docs/architecture-review.md practice
     * funnel design, stage 3). Idempotent: safe to re-run.
     */
    public function run(): void
    {
        $badges = [
            [
                'key' => 'first_challenge',
                'name' => 'First Steps',
                'description' => 'Completed your first coding challenge.',
                'icon' => '🎉',
            ],
            [
                'key' => 'streak_7',
                'name' => 'On a Roll',
                'description' => 'Practiced 7 days in a row.',
                'icon' => '🔥',
            ],
            [
                'key' => 'path_completed',
                'name' => 'Path Finisher',
                'description' => 'Completed every step of a learning path.',
                'icon' => '🏁',
            ],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(['key' => $badge['key']], $badge);
        }
    }
}
