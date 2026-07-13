<?php

namespace Database\Seeders;

use App\Models\Path;
use App\Models\PathStep;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Observability track, Phase A — seeds the "Observability 101" path with the
 * first incident-reader step (the N+1 query). The learner reads a real trace,
 * a latency metric, and a log line, then diagnoses the fault. Reuses the F5
 * quiz grading via the `quiz` column; the telemetry lives in `evidence`.
 *
 * NOTE: `concept_content` is intentionally left null. The step page renders
 * StepConceptView whenever concept_content is present, which would shadow the
 * incident branch — the scenario in the evidence is the step's intro instead.
 */
class IncidentSeeder extends Seeder
{
    public function run(): void
    {
        $consultant = User::where('email', 'consultant@consultant.com')->firstOrFail();

        $path = Path::firstOrCreate(
            ['name' => 'Observability 101', 'consultant_id' => $consultant->id],
            ['description' => 'Learn to operate what you build. Read production telemetry — traces, metrics, and logs — and diagnose real failures the way an on-call engineer does.'],
        );

        PathStep::updateOrCreate(
            ['path_id' => $path->id, 'order' => 0],
            [
                'title' => 'Incident: checkout is getting slower',
                'type' => 'incident',
                'difficulty' => 'intermediate',
                'estimated_minutes' => 10,
                'description' => 'Read the telemetry and find why checkout latency is climbing.',
                'concept_content' => null,
                'evidence' => [
                    'scenario' => 'Checkout p95 latency has climbed from ~200ms to 1.4s over the last 20 minutes. No deploy went out. Customers are complaining that "the buy button spins." Here is the telemetry for one slow request — read it and find the cause.',
                    'trace' => [
                        'root' => 'POST /checkout',
                        'spans' => [
                            ['id' => 'a', 'parent' => null, 'name' => 'POST /checkout', 'service' => 'web', 'start' => 0, 'dur' => 1400, 'kind' => 'server'],
                            ['id' => 'b', 'parent' => 'a', 'name' => 'AuthMiddleware', 'service' => 'web', 'start' => 2, 'dur' => 5, 'kind' => 'internal'],
                            ['id' => 'c', 'parent' => 'a', 'name' => 'CartController@checkout', 'service' => 'web', 'start' => 8, 'dur' => 1385, 'kind' => 'internal'],
                            ['id' => 'd', 'parent' => 'c', 'name' => 'SELECT carts WHERE id=?', 'service' => 'db', 'start' => 10, 'dur' => 8, 'kind' => 'db'],
                            ['id' => 'e', 'parent' => 'c', 'name' => 'SELECT cart_items WHERE cart_id=?', 'service' => 'db', 'start' => 20, 'dur' => 6, 'kind' => 'db'],
                            ['id' => 'n1', 'parent' => 'c', 'name' => 'SELECT products WHERE id=?', 'service' => 'db', 'start' => 30, 'dur' => 8, 'kind' => 'db', 'repeat' => 120],
                            ['id' => 'p', 'parent' => 'c', 'name' => 'POST payments-gateway', 'service' => 'ext', 'start' => 1210, 'dur' => 180, 'kind' => 'client'],
                        ],
                    ],
                    'metrics' => [
                        [
                            'title' => 'checkout p95 latency',
                            'unit' => 'ms',
                            'threshold' => 500,
                            'series' => [[0, 210], [4, 250], [8, 430], [12, 720], [16, 980], [20, 1400]],
                        ],
                    ],
                    'logs' => [
                        ['t' => '12:04:31', 'level' => 'INFO', 'request_id' => 'req_9f2', 'msg' => 'checkout started cart_id=8814 items=120'],
                        ['t' => '12:04:32', 'level' => 'WARN', 'request_id' => 'req_9f2', 'msg' => 'N+1 detected: SELECT products executed 120 times in a single request'],
                        ['t' => '12:04:32', 'level' => 'INFO', 'request_id' => 'req_9f2', 'msg' => 'checkout completed in 1402ms status=200'],
                    ],
                ],
                'quiz' => [
                    [
                        'id' => 1,
                        'question' => 'Looking at the trace, which operation is responsible for most of the 1.4s?',
                        'options' => [
                            'The external payment-gateway call',
                            'The repeated "SELECT products" lookups',
                            'The AuthMiddleware',
                            'The initial cart fetch',
                        ],
                        'correct_index' => 1,
                        'explanation' => 'The product lookup runs 120 times at ~8ms each (~960ms total) — far more than the 180ms payment call, even though a single payment span "looks" slow at a glance.',
                    ],
                    [
                        'id' => 2,
                        'question' => 'What is the root cause?',
                        'options' => [
                            'A missing database index',
                            'An N+1 query — one query per cart item',
                            'A slow external dependency',
                            'A memory leak in the worker',
                        ],
                        'correct_index' => 1,
                        'explanation' => 'One "SELECT products WHERE id=?" fires per cart item (120 items → 120 queries). The log even flags it. That is the classic N+1.',
                    ],
                    [
                        'id' => 3,
                        'question' => 'Which fix resolves it?',
                        'options' => [
                            'Increase PHP memory_limit',
                            'Add a read replica for the database',
                            'Eager-load the products (e.g. with(\'product\')) so they load in one query',
                            'Raise the checkout timeout',
                        ],
                        'correct_index' => 2,
                        'explanation' => 'Eager-loading collapses the 120 per-item queries into a single "WHERE id IN (...)" query. The other options mask symptoms without removing the N+1.',
                    ],
                ],
            ],
        );

        $this->command->info("Seeded 'Observability 101' with the N+1 incident step.");
    }
}
