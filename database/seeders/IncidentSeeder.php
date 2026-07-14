<?php

namespace Database\Seeders;

use App\Models\Path;
use App\Models\PathStep;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Observability track, Phase A — seeds the "Observability 101" path with a
 * library of incident-reader steps. Each hands the learner real telemetry
 * (a trace, metrics, and/or logs) and a fault to diagnose. They reuse the F5
 * quiz grading via the `quiz` column; the telemetry lives in `evidence`.
 *
 * The incidents teach deliberately DIFFERENT signals so the learner builds a
 * broad diagnostic reflex, not one trick:
 *   0. N+1 query          — many fast queries (trace-led)
 *   1. Missing index      — one slow query / full scan (trace-led; contrasts w/ N+1)
 *   2. Slow downstream    — external dependency + retries (resilience, not your code)
 *   3. Bad deploy         — error spike correlated to a deploy marker (metric/log-led)
 *
 * NOTE: `concept_content` is intentionally null — the step page renders
 * StepConceptView whenever it is present, which would shadow the incident
 * branch. The scenario in the evidence is the step's intro instead.
 */
class IncidentSeeder extends Seeder
{
    public function run(): void
    {
        $consultant = User::where('email', 'consultant@consultant.com')->firstOrFail();

        $path = Path::firstOrCreate(
            ['name' => 'Observability 101', 'consultant_id' => $consultant->id],
            [
                'description' => 'Learn to operate what you build. Read production telemetry — traces, metrics, and logs — and diagnose real failures the way an on-call engineer does.',
                // Completing every incident earns the Observability Engineer seal.
                'badge_key' => 'observability_certified',
            ],
        );

        // firstOrCreate won't update an existing row — ensure the seal is set
        // even if the path was seeded before this column existed.
        if ($path->badge_key !== 'observability_certified') {
            $path->update(['badge_key' => 'observability_certified']);
        }

        foreach ($this->incidents() as $incident) {
            PathStep::updateOrCreate(
                ['path_id' => $path->id, 'order' => $incident['order']],
                array_merge($incident, ['type' => 'incident', 'concept_content' => null]),
            );
        }

        $this->command->info("Seeded 'Observability 101' with ".count($this->incidents()).' incidents.');
    }

    private function incidents(): array
    {
        return [
            $this->nPlusOne(),
            $this->missingIndex(),
            $this->slowDownstream(),
            $this->badDeploy(),
            $this->cacheStampede(),
            $this->poolExhaustion(),
            $this->memoryLeak(),
        ];
    }

    private function nPlusOne(): array
    {
        return [
            'order' => 0,
            'title' => 'Incident: checkout is getting slower',
            'difficulty' => 'intermediate',
            'estimated_minutes' => 10,
            'description' => 'Read the telemetry and find why checkout latency is climbing.',
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
                    ['title' => 'checkout p95 latency', 'unit' => 'ms', 'threshold' => 500, 'series' => [[0, 210], [4, 250], [8, 430], [12, 720], [16, 980], [20, 1400]]],
                ],
                'logs' => [
                    ['t' => '12:04:31', 'level' => 'INFO', 'request_id' => 'req_9f2', 'msg' => 'checkout started cart_id=8814 items=120'],
                    ['t' => '12:04:32', 'level' => 'WARN', 'request_id' => 'req_9f2', 'msg' => 'N+1 detected: SELECT products executed 120 times in a single request'],
                    ['t' => '12:04:32', 'level' => 'INFO', 'request_id' => 'req_9f2', 'msg' => 'checkout completed in 1402ms status=200'],
                ],
            ],
            'quiz' => [
                ['id' => 1, 'question' => 'Looking at the trace, which operation is responsible for most of the 1.4s?', 'options' => ['The external payment-gateway call', 'The repeated "SELECT products" lookups', 'The AuthMiddleware', 'The initial cart fetch'], 'correct_index' => 1, 'explanation' => 'The product lookup runs 120 times at ~8ms each (~960ms total) — far more than the 180ms payment call, even though a single payment span "looks" slow at a glance.'],
                ['id' => 2, 'question' => 'What is the root cause?', 'options' => ['A missing database index', 'An N+1 query — one query per cart item', 'A slow external dependency', 'A memory leak in the worker'], 'correct_index' => 1, 'explanation' => 'One "SELECT products WHERE id=?" fires per cart item (120 items → 120 queries). The log even flags it. That is the classic N+1.'],
                ['id' => 3, 'question' => 'Which fix resolves it?', 'options' => ['Increase PHP memory_limit', 'Add a read replica for the database', 'Eager-load the products (e.g. with(\'product\')) so they load in one query', 'Raise the checkout timeout'], 'correct_index' => 2, 'explanation' => 'Eager-loading collapses the 120 per-item queries into a single "WHERE id IN (...)" query. The other options mask symptoms without removing the N+1.'],
            ],
        ];
    }

    private function missingIndex(): array
    {
        return [
            'order' => 1,
            'title' => 'Incident: the customer search page times out',
            'difficulty' => 'beginner',
            'estimated_minutes' => 10,
            'description' => 'One endpoint is slow while everything else is fast. Find out why.',
            'evidence' => [
                'scenario' => 'The admin "find customer by email" page times out, but every other page is fast. It started after the customers table grew past ~half a million rows. Here is a trace of one search request.',
                'trace' => [
                    'root' => 'GET /admin/customers/search',
                    'spans' => [
                        ['id' => 'a', 'parent' => null, 'name' => 'GET /admin/customers/search', 'service' => 'web', 'start' => 0, 'dur' => 2100, 'kind' => 'server'],
                        ['id' => 'b', 'parent' => 'a', 'name' => 'AuthMiddleware', 'service' => 'web', 'start' => 2, 'dur' => 4, 'kind' => 'internal'],
                        ['id' => 'c', 'parent' => 'a', 'name' => 'CustomerController@search', 'service' => 'web', 'start' => 8, 'dur' => 2090, 'kind' => 'internal'],
                        ['id' => 'q', 'parent' => 'c', 'name' => 'SELECT * FROM customers WHERE email=?', 'service' => 'db', 'start' => 14, 'dur' => 2075, 'kind' => 'db'],
                    ],
                ],
                'metrics' => [
                    ['title' => 'customer search p95', 'unit' => 'ms', 'threshold' => 500, 'series' => [[0, 180], [5, 600], [10, 1200], [15, 1800], [20, 2100]]],
                ],
                'logs' => [
                    ['t' => '09:11:02', 'level' => 'INFO', 'request_id' => 'req_a45', 'msg' => 'customer search email=jane@acme.io'],
                    ['t' => '09:11:04', 'level' => 'WARN', 'request_id' => 'req_a45', 'msg' => 'slow query 2075ms — full table scan (type=ALL, rows_examined=482113, no index on customers.email)'],
                ],
            ],
            'quiz' => [
                ['id' => 1, 'question' => 'Which operation dominates the request time?', 'options' => ['AuthMiddleware', 'The SELECT customers query', 'HTTP request parsing', 'JSON serialization'], 'correct_index' => 1, 'explanation' => 'A single span — the customers query — takes 2075ms of the 2100ms request. Everything else is negligible.'],
                ['id' => 2, 'question' => 'What is the root cause? Note there is exactly ONE slow query, not many.', 'options' => ['An N+1 query', 'A missing index — the query does a full table scan', 'A slow external dependency', 'A memory leak'], 'correct_index' => 1, 'explanation' => 'Unlike an N+1 (many fast queries), this is ONE query scanning 482k rows (type=ALL) because customers.email has no index. Reading the trace shape — one huge span vs. many small ones — is how you tell them apart.'],
                ['id' => 3, 'question' => 'Which fix resolves it?', 'options' => ['Eager-load a relation', 'Add an index on customers.email', 'Add a Redis cache in front of the query', 'Increase PHP memory_limit'], 'correct_index' => 1, 'explanation' => 'An index on customers.email turns the full scan into an index lookup — milliseconds instead of seconds. A cache would only hide the problem for repeated searches.'],
            ],
        ];
    }

    private function slowDownstream(): array
    {
        return [
            'order' => 2,
            'title' => 'Incident: checkout is slow, but the database looks fine',
            'difficulty' => 'intermediate',
            'estimated_minutes' => 12,
            'description' => 'The slowness is real, but it is not in your code. Find where it lives.',
            'evidence' => [
                'scenario' => 'Checkout is slow again — but this time the database spans are tiny and your code has not changed. The slowdown began right after your payments provider posted a status-page incident. Here is a trace.',
                'trace' => [
                    'root' => 'POST /checkout',
                    'spans' => [
                        ['id' => 'a', 'parent' => null, 'name' => 'POST /checkout', 'service' => 'web', 'start' => 0, 'dur' => 3260, 'kind' => 'server'],
                        ['id' => 'c', 'parent' => 'a', 'name' => 'CartController@checkout', 'service' => 'web', 'start' => 6, 'dur' => 3250, 'kind' => 'internal'],
                        ['id' => 'd', 'parent' => 'c', 'name' => 'SELECT cart + items', 'service' => 'db', 'start' => 10, 'dur' => 14, 'kind' => 'db'],
                        ['id' => 'r1', 'parent' => 'c', 'name' => 'POST payments-gateway (try 1)', 'service' => 'ext', 'start' => 30, 'dur' => 1000, 'kind' => 'client'],
                        ['id' => 'r2', 'parent' => 'c', 'name' => 'POST payments-gateway (try 2)', 'service' => 'ext', 'start' => 1035, 'dur' => 1000, 'kind' => 'client'],
                        ['id' => 'r3', 'parent' => 'c', 'name' => 'POST payments-gateway (try 3)', 'service' => 'ext', 'start' => 2040, 'dur' => 1150, 'kind' => 'client'],
                    ],
                ],
                'metrics' => [
                    ['title' => 'payments-gateway p95', 'unit' => 'ms', 'threshold' => 800, 'series' => [[0, 240], [5, 260], [10, 1900], [15, 3050], [20, 3200]]],
                ],
                'logs' => [
                    ['t' => '15:41:10', 'level' => 'WARN', 'request_id' => 'req_c71', 'msg' => 'payments-gateway timeout after 1000ms — retrying (2/3)'],
                    ['t' => '15:41:11', 'level' => 'WARN', 'request_id' => 'req_c71', 'msg' => 'payments-gateway timeout after 1000ms — retrying (3/3)'],
                    ['t' => '15:41:12', 'level' => 'INFO', 'request_id' => 'req_c71', 'msg' => 'payments-gateway ok on attempt 3 (2050ms spent on retries)'],
                ],
            ],
            'quiz' => [
                ['id' => 1, 'question' => 'Where is the request time going?', 'options' => ['The database queries', 'The payments-gateway calls, including retries', 'Auth and routing', 'JSON serialization'], 'correct_index' => 1, 'explanation' => 'The DB span is 14ms. The three payments-gateway attempts (two timing out, one succeeding) consume ~3.15s of the 3.26s request.'],
                ['id' => 2, 'question' => 'What is the root cause?', 'options' => ['An N+1 query', 'A missing index', 'A slow / failing external dependency', 'A memory leak'], 'correct_index' => 2, 'explanation' => 'Your DB and code are fine — the payments provider is timing out, and your client retries three times. The problem lives in someone else\'s system.'],
                ['id' => 3, 'question' => 'What is the best response? You cannot make their service faster.', 'options' => ['Add a database index', 'Add a short timeout + circuit breaker so you fail fast and degrade gracefully', 'Increase PHP memory_limit', 'Eager-load the products'], 'correct_index' => 1, 'explanation' => 'You cannot fix a third party\'s service. You protect YOUR service: a tight timeout, a circuit breaker to stop hammering a failing dependency, and a graceful fallback so one slow provider does not take checkout down.'],
            ],
        ];
    }

    private function badDeploy(): array
    {
        return [
            'order' => 3,
            'title' => 'Incident: error rate spiked out of nowhere',
            'difficulty' => 'intermediate',
            'estimated_minutes' => 10,
            'description' => 'No trace needed. Correlate the metric with the timeline and read the logs.',
            'evidence' => [
                'scenario' => 'The API 5xx error rate jumped from ~0.1% to 8% at 14:32. Traffic is normal, the database is healthy — but a deploy went out at 14:32. Use the metric and the logs to work out what happened and what to do first.',
                'metrics' => [
                    [
                        'title' => '5xx error rate',
                        'unit' => '%',
                        'threshold' => 1,
                        'annotations' => [['x' => 32, 'label' => 'deploy v2.4.1']],
                        'series' => [[20, 0.1], [26, 0.1], [30, 0.2], [32, 0.3], [34, 7.9], [40, 8.2], [46, 8.0]],
                    ],
                ],
                'logs' => [
                    ['t' => '14:32:04', 'level' => 'INFO', 'request_id' => 'deploy', 'msg' => 'release v2.4.1 deployed (git 7c1a9e2)'],
                    ['t' => '14:32:41', 'level' => 'ERROR', 'request_id' => 'req_e18', 'msg' => "TypeError: Cannot read property 'id' of null in OrderController@store:47"],
                    ['t' => '14:33:02', 'level' => 'ERROR', 'request_id' => 'req_e2a', 'msg' => "TypeError: Cannot read property 'id' of null in OrderController@store:47"],
                ],
            ],
            'quiz' => [
                ['id' => 1, 'question' => 'What changed at 14:32?', 'options' => ['A traffic spike', 'A deploy went out (release v2.4.1)', 'The database went down', 'The cache expired'], 'correct_index' => 1, 'explanation' => 'The error rate steps up exactly at the deploy annotation, and the log shows "release v2.4.1 deployed" at 14:32:04. That alignment is the clue.'],
                ['id' => 2, 'question' => 'What is the most likely root cause?', 'options' => ['An N+1 query', 'A code regression introduced by the new release', 'A slow external dependency', 'A missing index'], 'correct_index' => 1, 'explanation' => 'The errors are a brand-new null-reference in OrderController@store that appears only after v2.4.1 — a regression shipped in that release.'],
                ['id' => 3, 'question' => 'What is the fastest mitigation?', 'options' => ['Add a database index', 'Roll back the release', 'Increase PHP memory_limit', 'Add more web replicas'], 'correct_index' => 1, 'explanation' => 'With a bad deploy you roll back first to stop the bleeding, then debug the regression offline. Capacity or indexes cannot fix a code bug.'],
            ],
        ];
    }

    private function cacheStampede(): array
    {
        return [
            'order' => 4,
            'title' => 'Incident: the database spikes like clockwork',
            'difficulty' => 'advanced',
            'estimated_minutes' => 12,
            'description' => 'A periodic DB spike lines up with a cache expiry. Read the two metrics together.',
            'evidence' => [
                'scenario' => 'Every 5 minutes, like clockwork, database CPU jumps to ~100% and API latency spikes for a few seconds, then recovers. It lines up exactly with when a hot cache key expires. Look at the cache hit rate and DB CPU side by side.',
                'metrics' => [
                    ['title' => 'cache hit rate', 'unit' => '%', 'series' => [[0, 98], [4, 97], [5, 18], [6, 92], [9, 97], [10, 96], [11, 15], [12, 94]]],
                    ['title' => 'DB CPU', 'unit' => '%', 'threshold' => 80, 'series' => [[0, 31], [4, 33], [5, 99], [6, 68], [9, 34], [10, 32], [11, 98], [12, 61]]],
                ],
                'logs' => [
                    ['t' => '10:05:00', 'level' => 'INFO', 'request_id' => 'cache', 'msg' => "cache key 'homepage:featured' expired (ttl reached)"],
                    ['t' => '10:05:01', 'level' => 'WARN', 'request_id' => 'cache', 'msg' => '1,842 concurrent requests recomputing homepage:featured — DB CPU 99%'],
                    ['t' => '10:05:03', 'level' => 'INFO', 'request_id' => 'cache', 'msg' => "cache key 'homepage:featured' repopulated"],
                ],
            ],
            'quiz' => [
                ['id' => 1, 'question' => 'What do the two metrics show happening together at t=5?', 'options' => ['Only the database is slow', 'Cache hit rate collapses AND DB CPU spikes at the same instant', 'Only the cache is permanently broken', 'A deploy went out'], 'correct_index' => 1, 'explanation' => 'The two metrics move inversely at the exact same moment: the moment the cache stops serving (hit rate → ~15%), the DB has to serve everything (CPU → ~99%). Reading correlated metrics together is the skill here.'],
                ['id' => 2, 'question' => 'What is the root cause?', 'options' => ['A missing index', 'A cache stampede — many requests rebuild the same expired key at once', 'An N+1 query', 'A memory leak'], 'correct_index' => 1, 'explanation' => 'When the hot key expires, ~1,800 concurrent requests all miss and rebuild it simultaneously (a thundering herd), hammering the DB until one repopulates the cache.'],
                ['id' => 3, 'question' => 'What is the best fix?', 'options' => ['Increase PHP memory_limit', 'Lock the rebuild so only one request recomputes the key (or serve stale while revalidating), and jitter TTLs', 'Add a database index', 'Roll back the last deploy'], 'correct_index' => 1, 'explanation' => 'The DB is healthy — the problem is every request rebuilding one key at once. Serialize the rebuild with a mutex/lock, or serve stale-while-revalidate so one worker refreshes while others use the old value. Jittering TTLs stops many keys expiring together.'],
            ],
        ];
    }

    private function poolExhaustion(): array
    {
        return [
            'order' => 5,
            'title' => 'Incident: intermittent 500s, but the queries are fast',
            'difficulty' => 'advanced',
            'estimated_minutes' => 12,
            'description' => 'Some requests fail under load though the SQL itself is quick. Read where the time goes.',
            'evidence' => [
                'scenario' => 'Under peak traffic, some requests return 500s and others hang — yet the SQL queries themselves are fast. Here is a trace of one slow request, plus the DB connection metric.',
                'trace' => [
                    'root' => 'GET /api/orders',
                    'spans' => [
                        ['id' => 'a', 'parent' => null, 'name' => 'GET /api/orders', 'service' => 'web', 'start' => 0, 'dur' => 5200, 'kind' => 'server'],
                        ['id' => 'c', 'parent' => 'a', 'name' => 'OrderController@index', 'service' => 'web', 'start' => 4, 'dur' => 5190, 'kind' => 'internal'],
                        ['id' => 'w', 'parent' => 'c', 'name' => 'waiting for DB connection (pool)', 'service' => 'db', 'start' => 10, 'dur' => 5000, 'kind' => 'db'],
                        ['id' => 'q', 'parent' => 'c', 'name' => 'SELECT orders WHERE user_id=?', 'service' => 'db', 'start' => 5015, 'dur' => 12, 'kind' => 'db'],
                    ],
                ],
                'metrics' => [
                    ['title' => 'DB connections in use', 'unit' => '/20', 'threshold' => 20, 'series' => [[0, 8], [5, 14], [10, 20], [15, 20], [20, 20], [25, 20]]],
                ],
                'logs' => [
                    ['t' => '18:20:11', 'level' => 'WARN', 'request_id' => 'req_d90', 'msg' => 'waited 5000ms to acquire a connection from pool (max=20, in use=20)'],
                    ['t' => '18:20:16', 'level' => 'ERROR', 'request_id' => 'req_d91', 'msg' => 'SQLSTATE[HY000] [1040]: Too many connections'],
                ],
            ],
            'quiz' => [
                ['id' => 1, 'question' => 'In the trace, where is the 5.2s going?', 'options' => ['Executing the SQL query', 'Waiting to acquire a database connection', 'AuthMiddleware', 'Serializing the JSON response'], 'correct_index' => 1, 'explanation' => 'The query runs in 12ms. The request spends ~5s in the "waiting for DB connection" span — the time is in the wait, not the work. That distinction is the whole lesson.'],
                ['id' => 2, 'question' => 'What is the root cause?', 'options' => ['A slow query needing an index', 'Connection-pool exhaustion — every connection is already in use', 'An N+1 query', 'A slow external dependency'], 'correct_index' => 1, 'explanation' => 'The connections-in-use metric is pinned at the pool max (20) and the log says "Too many connections". New requests block waiting for a connection to free up.'],
                ['id' => 3, 'question' => 'What is the best fix?', 'options' => ['Add an index to the orders table', 'Raise the pool size and hunt for code holding connections too long (long transactions, leaks)', 'Increase the request timeout', 'Add a caching layer'], 'correct_index' => 1, 'explanation' => 'A 12ms query needs no index. Give the pool more headroom and — more importantly — find connections held open too long (long-running transactions, connections never released) so they return to the pool quickly.'],
            ],
        ];
    }

    private function memoryLeak(): array
    {
        return [
            'order' => 6,
            'title' => 'Incident: the queue worker keeps dying',
            'difficulty' => 'advanced',
            'estimated_minutes' => 12,
            'description' => 'A worker is OOM-killed on a schedule. Read the shape of its memory over time.',
            'evidence' => [
                'scenario' => 'A queue worker keeps getting killed and restarted roughly every 30 minutes. Jobs complete fine, but throughput dips at each restart. Here is the worker\'s memory over two hours (limit 512MB).',
                'metrics' => [
                    [
                        'title' => 'worker memory', 'unit' => 'MB', 'threshold' => 512,
                        'annotations' => [['x' => 30, 'label' => 'OOM restart'], ['x' => 60, 'label' => 'OOM restart'], ['x' => 90, 'label' => 'OOM restart']],
                        'series' => [[0, 120], [15, 300], [28, 505], [30, 92], [45, 310], [58, 508], [60, 95], [75, 320], [88, 506], [90, 98], [110, 360]],
                    ],
                ],
                'logs' => [
                    ['t' => '02:28:40', 'level' => 'WARN', 'request_id' => 'worker_7f3', 'msg' => 'memory usage 505MB / 512MB'],
                    ['t' => '02:30:02', 'level' => 'ERROR', 'request_id' => 'worker_7f3', 'msg' => 'worker killed (OOM), restarting'],
                    ['t' => '02:30:05', 'level' => 'INFO', 'request_id' => 'worker_8a1', 'msg' => 'worker restarted, memory 92MB'],
                ],
            ],
            'quiz' => [
                ['id' => 1, 'question' => 'What pattern does the memory metric show?', 'options' => ['A flat line', 'A sawtooth — climbing to the limit, then dropping at each restart', 'A one-time spike after a deploy', 'Random noise'], 'correct_index' => 1, 'explanation' => 'Memory climbs steadily to the 512MB cap, the worker is OOM-killed and restarts (dropping to ~90MB), then climbs again. That repeating sawtooth is the signature of a leak.'],
                ['id' => 2, 'question' => 'What is the root cause?', 'options' => ['A cache stampede', 'A memory leak — memory grows unbounded within the worker until it hits the limit', 'A missing index', 'A slow external dependency'], 'correct_index' => 1, 'explanation' => 'Something accumulates in memory as the worker processes jobs and is never released, so usage only ever grows until the process is killed.'],
                ['id' => 3, 'question' => 'What is the best fix?', 'options' => ['Just raise memory_limit to 2GB', 'Find and fix what accumulates (unbounded array/cache, uncleared static state, retained references)', 'Add a database index', 'Roll back the deploy'], 'correct_index' => 1, 'explanation' => 'Raising the limit only delays the crash — the leak still grows. Find what grows per job (a static array never cleared, an in-memory query log, event listeners piling up) and release it. Restarting workers periodically is a stopgap, not a fix.'],
            ],
        ];
    }
}
