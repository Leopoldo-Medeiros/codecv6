<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

/**
 * Seeds the entire curriculum (challenges + learning paths + steps) from the
 * versioned files under database/content/ via the idempotent `content:sync`
 * command — see docs/architecture-review.md Phase A.
 *
 * This replaces the old content-as-PHP-heredoc seeders (ChallengeSeeder,
 * LearningPathsSeeder, PathStepSeeder, IncidentSeeder, RestApiTrackSeeder,
 * ChallengeLinkSeeder). content:sync creates challenges before paths, so steps
 * that reference a challenge by slug resolve correctly.
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        Artisan::call('content:sync');

        $this->command?->getOutput()->write(Artisan::output());
    }
}
