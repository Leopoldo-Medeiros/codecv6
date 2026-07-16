<?php

namespace Database\Seeders;

use App\Models\PathStep;
use Illuminate\Database\Seeder;

class ChallengeLinkSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            [
                'path' => 'PHP for the Real World',
                'title' => 'Challenge: Find and Fix the Bugs in This Laravel API',
                'slug' => 'find-fix-laravel-api-bugs',
            ],
            [
                'path' => 'Debugging Like a Pro',
                'title' => 'Challenge: Resolve the Incident Using Only Logs',
                'slug' => 'resolve-incident-from-logs',
            ],
            [
                'path' => 'Debugging Like a Pro',
                'title' => 'Final Challenge: Debug Session — 3 Bugs, 60 Minutes',
                'slug' => 'final-debug-session-3-bugs',
            ],
            [
                'path' => 'APM with New Relic',
                'title' => 'Challenge: Your App is Slow — Find the Bottleneck in 30 Minutes',
                'slug' => 'find-api-bottleneck',
            ],
            [
                'path' => 'OpenTelemetry in Practice',
                'title' => 'Challenge: Instrument This API End-to-End',
                'slug' => 'instrument-api-end-to-end',
            ],
            [
                'path' => 'Full Stack Observability',
                'title' => 'Challenge: Trace a Bug from Frontend to Database',
                'slug' => 'trace-frontend-to-database',
            ],
            [
                'path' => 'Full Stack Observability',
                'title' => 'Final Challenge: Simulate a Complete Incident Response',
                'slug' => 'simulate-incident-response',
            ],
            [
                'path' => 'Database Performance',
                'title' => 'Challenge: Optimize This API Endpoint',
                'slug' => 'optimize-api-endpoint',
            ],
            [
                'path' => 'Git & Professional Workflow',
                'title' => 'Challenge: Untangle This Git History',
                'slug' => 'untangle-git-history',
            ],
        ];

        foreach ($links as $link) {
            $updated = PathStep::whereHas('path', fn ($q) => $q->where('name', $link['path']))
                ->where('title', $link['title'])
                ->update(['challenge_slug' => $link['slug']]);

            $status = $updated ? 'linked' : 'not found';
            $this->command->info("[{$status}] {$link['path']} / {$link['title']} → {$link['slug']}");
        }
    }
}
