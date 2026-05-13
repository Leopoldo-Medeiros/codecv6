<?php

namespace Database\Seeders;

use App\Models\Path;
use App\Models\PathStep;
use App\Models\User;
use Illuminate\Database\Seeder;

class PathStepSeeder extends Seeder
{
    public function run(): void
    {
        $consultant = User::where('email', 'consultant@consultant.com')->firstOrFail();

        foreach ($this->paths() as $pathData) {
            $steps = $pathData['steps'];
            unset($pathData['steps']);

            $path = Path::firstOrCreate(
                ['name' => $pathData['name'], 'consultant_id' => $consultant->id],
                ['description' => $pathData['description'], 'consultant_id' => $consultant->id],
            );

            foreach ($steps as $order => $step) {
                PathStep::create(array_merge($step, [
                    'path_id' => $path->id,
                    'order' => $order + 1,
                ]));
            }

            $this->command->info("Created path: {$path->name} with ".count($steps).' steps');
        }
    }

    private function paths(): array
    {
        return [
            // ─────────────────────────────────────────────────────────────
            // PATH 6 — PHP Code Katas
            // ─────────────────────────────────────────────────────────────
            [
                'name' => 'PHP Code Katas',
                'description' => 'Deliberate practice through short, focused coding exercises. Each kata targets a specific PHP language feature — null safety, enums, value objects, generators, and design patterns. The goal is not just to pass the tests; it is to internalise the concept deeply enough to apply it instinctively under pressure.',
                'steps' => [
                    [
                        'title' => 'The Philosophy of Code Katas',
                        'type' => 'reading',
                        'description' => 'A kata (型) is a martial arts term for a form practiced repeatedly until the movement becomes unconscious. In software, a code kata is a small, well-defined problem solved over and over — not to produce a result, but to build muscle memory. This module explains why deliberate practice beats tutorial consumption, how to get maximum benefit from each kata, and how professional developers maintain their edge through regular practice.',
                        'resources' => [
                            ['label' => 'The Art of Deliberate Practice — Anders Ericsson', 'url' => 'https://www.goodreads.com/book/show/26312997-peak'],
                            ['label' => 'Kata Catalogue — codingdojo.org', 'url' => 'https://codingdojo.org/kata/'],
                            ['label' => 'PHP Koans — practice through failing tests', 'url' => 'https://github.com/livetyping/php-koans'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => null,
                        'challenge_slug' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Kata: Null-Safe Property Chain',
                        'type' => 'challenge',
                        'description' => 'PHP 8.0 introduced `?->`, the null-safe operator. Instead of nesting `if ($x !== null)` guards, you short-circuit the entire chain the moment any link returns `null`. This kata builds the reflex for safe navigation through deep object graphs — a pattern you will use every day in production code.',
                        'resources' => [
                            ['label' => 'PHP 8.0: Null-safe operator', 'url' => 'https://www.php.net/releases/8.0/en.php#nullsafe-operator'],
                            ['label' => 'Null Object Pattern', 'url' => 'https://refactoring.guru/introduce-null-object'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => 'You are reviewing a PR. The previous developer wrote five nested `if` statements to safely navigate a User → Address → City chain. The reviewer left a comment: "PHP 8 has a better way." Rewrite it.',
                        'challenge_slug' => 'null-safe-property-chain',
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Kata: HTTP Status Labels with match',
                        'type' => 'challenge',
                        'description' => 'PHP 8.0 `match` is not just a cleaner `switch` — it uses strict comparison, returns a value, and throws `\UnhandledMatchError` for unmatched arms instead of silently falling through. This kata trains the habit of reaching for `match` any time you need a value-to-value mapping with exhaustive coverage.',
                        'resources' => [
                            ['label' => 'PHP 8.0 match expression', 'url' => 'https://www.php.net/manual/en/control-structures.match.php'],
                            ['label' => 'match vs switch — key differences', 'url' => 'https://php.watch/versions/8.0/match-expression'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => 'Your team\'s API returns numeric HTTP status codes in JSON. The frontend team asks for human-readable labels. A junior devised a 30-line switch statement. Write the `match` version in under 15 lines.',
                        'challenge_slug' => 'http-status-match',
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Kata: Readonly Money Value Object',
                        'type' => 'challenge',
                        'description' => 'Immutability eliminates an entire class of bugs: two parts of the code holding a reference to the same object and mutating it from one side without the other knowing. PHP 8.1 `readonly` enforces this at the language level, making it impossible to mutate a property after construction. This kata builds the pattern for Value Objects — one of the most useful tools in domain-driven design.',
                        'resources' => [
                            ['label' => 'PHP 8.1 readonly properties', 'url' => 'https://php.watch/versions/8.1/readonly'],
                            ['label' => 'Value Objects in DDD', 'url' => 'https://martinfowler.com/bliki/ValueObject.html'],
                            ['label' => 'Money pattern', 'url' => 'https://www.martinfowler.com/eaaCatalog/money.html'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => 'A financial system stores prices as integers (cents) to avoid floating-point errors. The current code passes raw ints everywhere and the currency gets mixed up. Introduce a `Money` Value Object that makes currency mismatches a compile-time error and formatting a single method call.',
                        'challenge_slug' => 'readonly-money-value-object',
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Kata: Memory-Efficient Generator Pagination',
                        'type' => 'challenge',
                        'description' => 'A background job that exports 2 million rows by loading them all into memory will crash in production. PHP generators allow you to process arbitrarily large datasets with constant memory: yield one row at a time, only fetching the next page when the consumer asks for it. This kata trains the mental model of lazy evaluation.',
                        'resources' => [
                            ['label' => 'PHP Generators — official docs', 'url' => 'https://www.php.net/manual/en/language.generators.overview.php'],
                            ['label' => 'Generators in depth', 'url' => 'https://www.php.net/manual/en/language.generators.syntax.php'],
                            ['label' => 'Memory usage in PHP — practical guide', 'url' => 'https://tideways.com/profiler/blog/how-much-memory-does-your-php-application-use'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => 'A nightly report job is failing with "Allowed memory size exhausted" on a customer with 800k records. Loading everything into an array is not an option. Rewrite the data-fetching layer using a generator so it never holds more than one page in memory.',
                        'challenge_slug' => 'generator-paginated-rows',
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Kata: Enum-Driven State Machine',
                        'type' => 'challenge',
                        'description' => 'State machines are everywhere: invoice workflows, order lifecycles, subscription statuses. Encoding valid transitions inside the enum that represents those states is elegant and safe — the compiler enforces that you handle every case, and invalid transitions return `null` instead of corrupting data. This is a premium kata that simulates a real domain modeling challenge.',
                        'resources' => [
                            ['label' => 'PHP 8.1 Enums', 'url' => 'https://www.php.net/manual/en/language.enumerations.php'],
                            ['label' => 'State Machine pattern', 'url' => 'https://refactoring.guru/design-patterns/state'],
                            ['label' => 'Domain Modelling Made Functional', 'url' => 'https://pragprog.com/titles/swdddf/domain-modeling-made-functional/'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => 'The billing team discovered that invoices can be moved from any state to any other state — there is no validation. Draft invoices are being marked as Paid directly, skipping the approval step. Model the invoice lifecycle as a proper state machine where illegal transitions are simply not possible.',
                        'challenge_slug' => 'enum-driven-state-machine',
                        'lab_url' => null,
                    ],
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // PATH 7 — Database Performance
            // ─────────────────────────────────────────────────────────────
            [
                'name' => 'Database Performance',
                'description' => 'Most PHP applications are slow because of the database, not the PHP code. A query that returns in 2ms on the developer\'s laptop with 500 rows returns in 8 seconds on production with 3 million rows. This path gives you the mental model and practical skills to write queries that scale: from understanding the query planner to Eloquent optimization patterns you can apply immediately.',
                'steps' => [
                    [
                        'title' => 'How the MySQL Query Planner Works',
                        'type' => 'reading',
                        'description' => 'Before MySQL runs a query, the query planner evaluates several execution strategies and picks the cheapest one — based on row estimates, index statistics, and join order. Understanding how it thinks is the foundation of all query optimization. We will cover the concept of cardinality (how selective an index is), how the planner chooses between a full table scan and an index seek, and why its estimates are sometimes wrong — and what to do about it.',
                        'resources' => [
                            ['label' => 'MySQL Query Optimization', 'url' => 'https://dev.mysql.com/doc/refman/8.0/en/query-optimization.html'],
                            ['label' => 'How MySQL Chooses Indexes', 'url' => 'https://dev.mysql.com/doc/refman/8.0/en/how-mysql-uses-indexes.html'],
                            ['label' => 'Use The Index, Luke — free book', 'url' => 'https://use-the-index-luke.com/'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => null,
                        'challenge_slug' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Index Design: When to Add, When to Avoid',
                        'type' => 'reading',
                        'description' => 'Indexes are not free — every write operation must update every index on the table. Adding an index to every column is not a solution; it is a different problem. We will cover composite indexes and the left-prefix rule, covering indexes that eliminate table lookups, partial indexes for sparse data, and the specific conditions under which an index will be silently ignored by the planner (functions on indexed columns, implicit type casts, leading wildcards in LIKE).',
                        'resources' => [
                            ['label' => 'MySQL Index Types', 'url' => 'https://dev.mysql.com/doc/refman/8.0/en/optimization-indexes.html'],
                            ['label' => 'Composite Index Strategy', 'url' => 'https://use-the-index-luke.com/sql/where-clause/the-equals-operator/concatenated-keys'],
                            ['label' => 'Laravel migration index methods', 'url' => 'https://laravel.com/docs/migrations#available-index-types'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => null,
                        'challenge_slug' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Lab: Diagnosing Slow Queries with EXPLAIN',
                        'type' => 'lab',
                        'description' => '`EXPLAIN` is the single most important tool in database optimization — it shows you exactly what the planner decided to do with your query. `EXPLAIN ANALYZE` runs the query and shows what it actually did versus what it expected. We will use both to identify full table scans, poor index selection, and unexpected filesorts on a real Laravel application.',
                        'resources' => [
                            ['label' => 'MySQL EXPLAIN output format', 'url' => 'https://dev.mysql.com/doc/refman/8.0/en/explain-output.html'],
                            ['label' => 'EXPLAIN ANALYZE', 'url' => 'https://dev.mysql.com/doc/refman/8.0/en/explain.html'],
                            ['label' => 'Laravel Telescope — query monitoring', 'url' => 'https://laravel.com/docs/telescope'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Enable the slow query log: set `slow_query_log = 1` and `long_query_time = 0.1` in MySQL config'],
                            ['id' => 2, 'text' => 'Run a test endpoint that you suspect is slow — generate at least 100 requests with a load tool'],
                            ['id' => 3, 'text' => 'Identify the slowest query in the slow query log and copy it'],
                            ['id' => 4, 'text' => 'Run `EXPLAIN FORMAT=JSON <your query>` and read the output — note the `type`, `rows`, and `key` fields'],
                            ['id' => 5, 'text' => 'If type is "ALL" (full scan), identify which column is used in the WHERE clause and is missing an index'],
                            ['id' => 6, 'text' => 'Add the index via a Laravel migration and run EXPLAIN again — confirm `type` changed to `ref` or `range`'],
                            ['id' => 7, 'text' => 'Measure: re-run the load test and compare the p95 latency before and after'],
                        ],
                        'challenge_prompt' => null,
                        'challenge_slug' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Eloquent Anti-Patterns: N+1, Fat Queries, and Cartesian Products',
                        'type' => 'reading',
                        'description' => 'The N+1 problem is well-known, but it is only one of many Eloquent anti-patterns. We will also cover: loading entire relationships when you only need one column (fat queries), Cartesian products from joining without proper constraints, counting via `count($collection)` on already-paginated results, and using `whereIn` with thousands of IDs. Each one looks innocent in development and destroys performance at scale.',
                        'resources' => [
                            ['label' => 'Eloquent Eager Loading', 'url' => 'https://laravel.com/docs/eloquent-relationships#eager-loading'],
                            ['label' => 'Laravel Query Builder', 'url' => 'https://laravel.com/docs/queries'],
                            ['label' => 'Detecting N+1 with Laravel Debugbar', 'url' => 'https://github.com/barryvdh/laravel-debugbar'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => null,
                        'challenge_slug' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Lab: Refactoring a Reporting Query from 12s to under 200ms',
                        'type' => 'lab',
                        'description' => 'A real-world reporting query with multiple JOINs, aggregates, and no indexes. We will work through the optimization process methodically: read the query plan, identify the bottleneck at each step, add indexes and restructure the query, and measure the improvement. By the end, you will have a repeatable process for attacking any slow query.',
                        'resources' => [
                            ['label' => 'Laravel DB::select and raw queries', 'url' => 'https://laravel.com/docs/database#running-queries'],
                            ['label' => 'Query caching strategies', 'url' => 'https://laravel.com/docs/cache'],
                            ['label' => 'Database indexing strategy guide', 'url' => 'https://use-the-index-luke.com/sql/where-clause'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Open the provided reporting endpoint and run it against a seeded dataset of 100k rows — note the response time'],
                            ['id' => 2, 'text' => 'Enable `DB::listen()` temporarily to capture every query executed during the request'],
                            ['id' => 3, 'text' => 'Run `EXPLAIN` on the slowest query — identify the full scan'],
                            ['id' => 4, 'text' => 'Add a composite index covering the WHERE and ORDER BY columns: `$table->index([\'user_id\', \'created_at\'])`'],
                            ['id' => 5, 'text' => 'Rewrite any N+1 found with `with()` or `withCount()`'],
                            ['id' => 6, 'text' => 'Move expensive aggregates to a database view or a dedicated summary table updated via a scheduled job'],
                            ['id' => 7, 'text' => 'Add a Redis cache layer with a 5-minute TTL for data that does not need to be real-time'],
                        ],
                        'challenge_prompt' => null,
                        'challenge_slug' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Challenge: Optimize This API Endpoint',
                        'type' => 'challenge',
                        'description' => 'A product listings endpoint returns in 6.4 seconds at p95. You have the source code, EXPLAIN output, and a benchmark script. The target SLA is 200ms. No infrastructure changes — the solution must be in the code and schema.',
                        'resources' => [
                            ['label' => 'Laravel Artisan tinker for query testing', 'url' => 'https://laravel.com/docs/artisan#tinker'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Clone the challenge repository and seed 100k products with realistic data distributions'],
                            ['id' => 2, 'text' => 'Run the benchmark: `ab -n 100 -c 10 http://localhost/api/products` — record the p95 baseline'],
                            ['id' => 3, 'text' => 'Enable `DB::enableQueryLog()` and identify every query the endpoint executes'],
                            ['id' => 4, 'text' => 'Run EXPLAIN on each query — document the scan type, rows examined, and whether an index is used'],
                            ['id' => 5, 'text' => 'Fix all issues found (indexes, eager loading, unnecessary columns, missing pagination)'],
                            ['id' => 6, 'text' => 'Re-run the benchmark and confirm p95 < 200ms'],
                            ['id' => 7, 'text' => 'Write a 1-page technical report: what you found, why it was slow, what you changed, and the measured improvement'],
                        ],
                        'challenge_prompt' => 'The VP of Product sent a screenshot from PageSpeed Insights: product listing page, 6.4 second TTFB, Performance score 23. The e-commerce launch is in 3 days. You have been asked to fix it without provisioning new infrastructure. What is your plan?',
                        'challenge_slug' => null,
                        'lab_url' => null,
                    ],
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // PATH 8 — Git & Professional Workflow
            // ─────────────────────────────────────────────────────────────
            [
                'name' => 'Git & Professional Workflow',
                'description' => 'Git is not just version control — it is the shared history of a team. A clean, readable commit history documents every architectural decision. An unreadable history with "fix", "update", "wip" commits is technical debt before the code is even reviewed. This path covers Git internals, branching strategies, PR workflows, automated quality gates, and the professional habits that distinguish senior developers.',
                'steps' => [
                    [
                        'title' => 'Git Internals: Objects, Trees, and Refs',
                        'type' => 'reading',
                        'description' => 'Most developers use Git as a black box — `git add`, `git commit`, `git push` — and are lost when something goes wrong. Understanding what Git actually stores changes everything: you can recover from any state, understand why rebase works the way it does, and never be afraid of the repository again. We will explore the four object types (blob, tree, commit, tag), how refs and the reflog work, and what HEAD really is.',
                        'resources' => [
                            ['label' => 'Git Internals — Pro Git book', 'url' => 'https://git-scm.com/book/en/v2/Git-Internals-Git-Objects'],
                            ['label' => 'Visualising Git', 'url' => 'https://visualizing-git.io/'],
                            ['label' => 'Git from the inside out', 'url' => 'https://maryrosecook.com/blog/post/git-from-the-inside-out'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => null,
                        'challenge_slug' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Lab: Building Your Branching Strategy',
                        'type' => 'lab',
                        'description' => 'Branching strategy is not a git question — it is a team coordination question. Trunk-based development reduces merge conflicts and enables continuous delivery; Gitflow supports long-running releases with separate hotfix lanes. Neither is universally correct. In this lab you will implement trunk-based development for a solo or small-team context and experience how short-lived branches eliminate the integration hell of long-lived feature branches.',
                        'resources' => [
                            ['label' => 'Trunk-Based Development', 'url' => 'https://trunkbaseddevelopment.com/'],
                            ['label' => 'Gitflow Workflow', 'url' => 'https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow'],
                            ['label' => 'GitHub Flow', 'url' => 'https://docs.github.com/en/get-started/using-github/github-flow'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Set up a test repository with a `main` branch protected: require PR review, passing CI, no direct push'],
                            ['id' => 2, 'text' => 'Create a feature branch named `feature/<issue-id>-short-description` from an up-to-date `main`'],
                            ['id' => 3, 'text' => 'Implement a small change (a new function with a test), commit with a conventional commit message'],
                            ['id' => 4, 'text' => 'While your branch is open, simulate a teammate merging to main — rebase your branch on the new main'],
                            ['id' => 5, 'text' => 'Open a Pull Request: write a description with "Why", "What changed", and a test plan'],
                            ['id' => 6, 'text' => 'Squash-merge the PR — verify that `main`\'s history has one clean, descriptive commit'],
                            ['id' => 7, 'text' => 'Delete the feature branch. Repeat with a hotfix branch directly from the latest release tag'],
                        ],
                        'challenge_prompt' => null,
                        'challenge_slug' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Writing Commits That Document Intent',
                        'type' => 'reading',
                        'description' => '"fix bug" is not a commit message — it is noise. Great commit messages are the primary documentation of why the code is the way it is. Six months from now, `git blame` will point you here, and your message will either save you an hour of archaeology or leave you guessing. We will cover the Conventional Commits specification, the anatomy of a commit with a body that explains the WHY, and how atomic commits make `git bisect` and `git revert` practical tools rather than last resorts.',
                        'resources' => [
                            ['label' => 'Conventional Commits specification', 'url' => 'https://www.conventionalcommits.org/'],
                            ['label' => 'How to Write a Git Commit Message', 'url' => 'https://cbea.ms/git-commit/'],
                            ['label' => 'git bisect documentation', 'url' => 'https://git-scm.com/docs/git-bisect'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => null,
                        'challenge_slug' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Lab: Code Review with GitHub Pull Requests',
                        'type' => 'lab',
                        'description' => 'Code review is one of the highest-leverage activities in a software team — it spreads knowledge, catches bugs before production, and enforces standards. But most code reviews are shallow ("LGTM 👍"). We will practice both sides: writing PRs that are easy to review (small scope, clear description, tests included) and giving reviews that add value (specific, actionable, context-aware comments).',
                        'resources' => [
                            ['label' => 'Google Engineering Practices — Code Review', 'url' => 'https://google.github.io/eng-practices/review/'],
                            ['label' => 'How to write useful PR descriptions', 'url' => 'https://github.blog/2015-01-21-how-to-write-the-perfect-pull-request/'],
                            ['label' => 'GitHub pull request review features', 'url' => 'https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/reviewing-changes-in-pull-requests/reviewing-proposed-changes-in-a-pull-request'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Open the provided repository and review the open PR — add at least 5 specific, constructive comments'],
                            ['id' => 2, 'text' => 'Distinguish between: blocking issues (code is wrong), suggestions (could be better), and nits (style, minor)'],
                            ['id' => 3, 'text' => 'Identify any missing tests — leave a comment explaining what scenario is not covered'],
                            ['id' => 4, 'text' => 'Approve or request changes with a written summary of your overall assessment'],
                            ['id' => 5, 'text' => 'Now write your own PR for a small feature: description must include "Why", "What", "How to test"'],
                            ['id' => 6, 'text' => 'Exchange PRs with a peer — receive feedback, respond to each comment, and update the code'],
                        ],
                        'challenge_prompt' => null,
                        'challenge_slug' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Lab: Automating Quality with pre-commit Hooks and CI',
                        'type' => 'lab',
                        'description' => 'Humans forget to run the linter. Humans skip the tests when they are in a hurry. CI does not forget. By automating quality checks — formatting, static analysis, tests — at both the pre-commit hook and CI pipeline level, you remove the burden from humans and make quality the path of least resistance. We will set up a complete quality gate using GitHub Actions.',
                        'resources' => [
                            ['label' => 'Git Hooks', 'url' => 'https://git-scm.com/book/en/v2/Customizing-Git-Git-Hooks'],
                            ['label' => 'GitHub Actions — getting started', 'url' => 'https://docs.github.com/en/actions/quickstart'],
                            ['label' => 'Laravel Pint — code formatter', 'url' => 'https://laravel.com/docs/pint'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Create a pre-commit hook (`.git/hooks/pre-commit`) that runs `./vendor/bin/pint --dirty` and fails if there are unfixed issues'],
                            ['id' => 2, 'text' => 'Add a pre-push hook that runs `php artisan test --compact` — the push is blocked if tests fail'],
                            ['id' => 3, 'text' => 'Create `.github/workflows/ci.yml` with three jobs: lint, test, and static-analysis (PHPStan or Larastan)'],
                            ['id' => 4, 'text' => 'Configure branch protection: PRs can only be merged if all three CI jobs pass'],
                            ['id' => 5, 'text' => 'Test the setup: intentionally introduce a formatting error, commit, and confirm the hook blocks you'],
                            ['id' => 6, 'text' => 'Push the broken code — confirm the CI pipeline also catches it before it reaches `main`'],
                        ],
                        'challenge_prompt' => null,
                        'challenge_slug' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Challenge: Untangle This Git History',
                        'type' => 'challenge',
                        'description' => 'A repository has been used without any conventions for 6 months. The history is a mix of merge commits, "fix", "wip", "test2" commits, and a feature that was half-implemented and never finished. Your task: clean up a branch so it is ready for a production merge, using interactive rebase, fixup commits, and a proper PR description.',
                        'resources' => [
                            ['label' => 'git rebase --interactive', 'url' => 'https://git-scm.com/docs/git-rebase'],
                            ['label' => 'git commit --fixup', 'url' => 'https://git-scm.com/docs/git-commit#Documentation/git-commit.txt---fixupamaboreredit-1ltcommitgt'],
                            ['label' => 'Rewriting History — Pro Git', 'url' => 'https://git-scm.com/book/en/v2/Git-Tools-Rewriting-History'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Clone the challenge repository and inspect the log: `git log --oneline --graph main..feature/payment`'],
                            ['id' => 2, 'text' => 'Identify which commits belong together semantically (e.g., "fix" commits that are really part of a prior commit)'],
                            ['id' => 3, 'text' => 'Use `git rebase -i main` to squash, fixup, reorder, and rename commits — aim for 3-5 clean commits'],
                            ['id' => 4, 'text' => 'Each final commit must: pass the tests, have a Conventional Commit message, and contain only logically related changes'],
                            ['id' => 5, 'text' => 'Write a PR description for the cleaned branch: what it does, why, and how to test it'],
                            ['id' => 6, 'text' => 'Push the clean branch and verify the CI pipeline passes end-to-end'],
                        ],
                        'challenge_prompt' => 'You have inherited a repository from a developer who just left the company. There is a payment feature branch that "is almost done" according to their last message 3 weeks ago. Before you can merge it, you need to understand it — and the history looks like a war zone. Clean it up.',
                        'challenge_slug' => null,
                        'lab_url' => null,
                    ],
                ],
            ],
        ];
    }
}
