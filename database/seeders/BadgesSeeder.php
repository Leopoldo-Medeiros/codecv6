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
                'category' => 'achievement',
                'name' => 'First Steps',
                'description' => 'Completed your first coding challenge.',
                'icon' => '🎉',
            ],
            [
                'key' => 'streak_7',
                'category' => 'achievement',
                'name' => 'On a Roll',
                'description' => 'Practiced 7 days in a row.',
                'icon' => '🔥',
            ],
            [
                'key' => 'path_completed',
                'category' => 'achievement',
                'name' => 'Path Finisher',
                'description' => 'Completed every step of a learning path.',
                'icon' => '🏁',
            ],
            [
                'key' => 'incident_solved',
                'category' => 'achievement',
                'name' => 'First Responder',
                'description' => 'Diagnosed your first production incident from telemetry.',
                'icon' => '🚨',
            ],
            // Certification — a credential employers can trust, earned by
            // completing the Observability track (see IncidentSeeder's
            // Path->badge_key). Rendered as a prominent seal on the profile.
            [
                'key' => 'observability_certified',
                'category' => 'certification',
                'name' => 'Observability Engineer',
                'description' => 'Completed the Observability track — can read traces, metrics, and logs to diagnose production incidents.',
                'icon' => '📡',
            ],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(['key' => $badge['key']], $badge);
        }
    }
}
