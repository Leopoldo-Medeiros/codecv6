<?php

namespace Database\Seeders;

use App\Models\Path;
use App\Models\PathStep;
use App\Models\User;
use Illuminate\Database\Seeder;

class LearningPathsSeeder extends Seeder
{
    public function run(): void
    {
        $consultant = User::where('email', 'consultant@consultant.com')->firstOrFail();

        foreach ($this->paths() as $pathData) {
            $steps = $pathData['steps'];
            unset($pathData['steps']);

            $path = Path::create([
                'name' => $pathData['name'],
                'description' => $pathData['description'],
                'consultant_id' => $consultant->id,
            ]);

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
            // PATH 1 — PHP for the Real World
            // ─────────────────────────────────────────────────────────────
            [
                'name' => 'PHP for the Real World',
                'description' => 'Solid foundations of modern PHP and Laravel with a focus on readable, testable, production-ready code. You will learn not just to write code that works, but code that other developers can maintain.',
                'steps' => [
                    [
                        'title' => 'Modern PHP: Types, Nullables and Enums',
                        'type' => 'reading',
                        'description' => 'PHP 8+ introduced an expressive type system that eliminates an entire class of bugs before the code even runs. In this module you will understand typed properties, union types, intersection types, string/int-backed enums, and how strict_types mode changes the interpreter\'s behaviour. The difference between PHP code from 2015 and 2024 starts here.',
                        'resources' => [
                            ['label' => 'PHP 8.3 Type System', 'url' => 'https://www.php.net/manual/en/language.types.declarations.php'],
                            ['label' => 'PHP Enums — official docs', 'url' => 'https://www.php.net/manual/en/language.enumerations.php'],
                            ['label' => 'Typed Properties RFC', 'url' => 'https://wiki.php.net/rfc/typed_properties_v2'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Laravel Request Lifecycle: What Happens Before Your Code Runs',
                        'type' => 'reading',
                        'description' => <<<'EOT'
                        Most juniors treat Laravel as magic. Understanding the lifecycle — from index.php to the Response — is what separates problem-solvers from tutorial-followers. We will dissect the bootstrap process, Service Providers, the IoC Container, and how middlewares form a pipeline.

                        ## The Full Request Journey

                        ```mermaid
                        flowchart TD
                            REQ(["🌐 HTTP Request"]):::emerald

                            REQ --> ENTRY["public/index.php\nApp entry point"]:::slate
                            ENTRY --> BOOTSTRAP["bootstrap/app.php\ncreates Application"]:::slate
                            BOOTSTRAP --> PROVIDERS["Service Providers\nregister() → boot()"]:::blue

                            PROVIDERS --> KERNEL["HTTP Kernel\nhandle()"]:::slate
                            KERNEL --> GMID["Global Middleware Pipeline\n(TrimStrings, VerifyCsrf…)"]:::slate
                            GMID --> ROUTER["Router\nmatches URI + HTTP verb"]:::emerald

                            ROUTER --> RMID["Route Middleware\n(auth, throttle, role…)"]:::slate
                            RMID --> CTRL["Controller Action\n← your code runs here"]:::emerald
                            CTRL --> RES["Eloquent Resource\nshapes the response"]:::emerald
                            RES --> RESP(["✅ HTTP Response\nJSON / HTML / Redirect"]):::emerald

                            classDef emerald fill:#d1fae5,stroke:#059669,color:#065f46,font-weight:600
                            classDef slate   fill:#f1f5f9,stroke:#64748b,color:#1e293b,font-weight:500
                            classDef blue    fill:#dbeafe,stroke:#3b82f6,color:#1e40af,font-weight:500
                        ```

                        Understanding where your code sits in this chain is what separates a problem-solver from someone who just adds `dd()` and hopes for the best.
                        EOT,
                        'resources' => [
                            ['label' => 'Laravel Request Lifecycle', 'url' => 'https://laravel.com/docs/lifecycle'],
                            ['label' => 'Service Container', 'url' => 'https://laravel.com/docs/container'],
                            ['label' => 'Service Providers', 'url' => 'https://laravel.com/docs/providers'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Advanced Eloquent: Scopes, Accessors and Events',
                        'type' => 'reading',
                        'description' => 'Eloquent is powerful but easily misused. We will cover local and global query scopes to encapsulate business rules in queries, accessors/mutators with custom casts, and model events (creating, saved, deleted) for logic that needs to trigger on state changes. You will also learn when NOT to use Eloquent and write raw SQL instead.',
                        'resources' => [
                            ['label' => 'Eloquent Scopes', 'url' => 'https://laravel.com/docs/eloquent#query-scopes'],
                            ['label' => 'Eloquent Mutators & Casting', 'url' => 'https://laravel.com/docs/eloquent-mutators'],
                            ['label' => 'Model Events & Observers', 'url' => 'https://laravel.com/docs/eloquent#events'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Lab: Build a RESTful API with Authentication',
                        'type' => 'lab',
                        'description' => 'Put everything into practice by building a task management API: Sanctum authentication, full CRUD, authorization policies, Form Requests with validation, API Resources for response transformation, and pagination. The focus is not on speed — it is on making every decision intentionally.',
                        'resources' => [
                            ['label' => 'Laravel Sanctum', 'url' => 'https://laravel.com/docs/sanctum'],
                            ['label' => 'API Resources', 'url' => 'https://laravel.com/docs/eloquent-resources'],
                            ['label' => 'Form Request Validation', 'url' => 'https://laravel.com/docs/validation#form-request-validation'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Create a new Laravel project: `composer create-project laravel/laravel task-api`'],
                            ['id' => 2, 'text' => 'Configure Sanctum and create the users migration with roles (admin, user)'],
                            ['id' => 3, 'text' => 'Create the Task model with: title, description, status (pending/in_progress/done), due_date, user_id'],
                            ['id' => 4, 'text' => 'Implement TaskController with index, store, show, update, destroy — using Form Request for validation'],
                            ['id' => 5, 'text' => 'Add a Policy: users can only view and edit their own tasks'],
                            ['id' => 6, 'text' => 'Create TaskResource with date transformation (Carbon) and computed field `is_overdue`'],
                            ['id' => 7, 'text' => 'Write 3 Feature Tests covering: creating a task, listing only own tasks, and an unauthorised attempt'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Code Review: What a Senior Would See in Your Code',
                        'type' => 'reading',
                        'description' => 'You wrote the code, the tests pass, but the PR was rejected. Why? This module covers patterns seniors look for in code reviews: N+1 queries, fat controllers vs thin controllers, methods with multiple responsibilities, names that lie, and the principle of least surprise. You will review real bad code and rewrite it with the right mindset.',
                        'resources' => [
                            ['label' => 'Clean Code — Robert C. Martin (summary)', 'url' => 'https://gist.github.com/wojteklu/73c6914cc446146b8b533c0988cf8d29'],
                            ['label' => 'Laravel Best Practices', 'url' => 'https://github.com/alexeymezenin/laravel-best-practices'],
                            ['label' => 'SOLID in PHP — practical examples', 'url' => 'https://www.youtube.com/watch?v=_jDNAcej0JE'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Challenge: Find and Fix the Bugs in This Laravel API',
                        'type' => 'challenge',
                        'description' => 'An e-commerce API is in production with critical bugs reported by users. You have access to the code and the logs. No stack trace provided — you need to find, reproduce and fix each problem. This is the real work of a backend developer.',
                        'resources' => [
                            ['label' => 'Laravel Debugging with dd() and dump()', 'url' => 'https://laravel.com/docs/helpers#method-dd'],
                            ['label' => 'PHP Error Levels', 'url' => 'https://www.php.net/manual/en/errorfunc.constants.php'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Clone the challenge repository: github.com/codecv/challenge-laravel-bugs'],
                            ['id' => 2, 'text' => 'Read the README with each user-reported bug description'],
                            ['id' => 3, 'text' => 'Bug #1: "Orders appear duplicated at checkout" — find the root cause'],
                            ['id' => 4, 'text' => 'Bug #2: "Admin can delete their own account" — fix the authorisation'],
                            ['id' => 5, 'text' => 'Bug #3: "API returns 500 when product is out of stock" — add proper error handling'],
                            ['id' => 6, 'text' => 'Write a regression test for each fixed bug'],
                            ['id' => 7, 'text' => 'Open a PR with a description explaining: root cause, impact and solution'],
                        ],
                        'challenge_prompt' => 'You are the only developer on call. It is 11pm and support has reported 3 critical production bugs affecting the checkout. The CEO is awake. You have 2 hours. Document every step — your investigation will be reviewed tomorrow.',
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Testing Mindset: Tests That Actually Protect',
                        'type' => 'lab',
                        'description' => 'Poorly written tests give false confidence and slow down development. We will cover the difference between unit, feature and integration tests in the Laravel context, when to use mocks vs a real database, how to structure factories with states, and how to write assertions that capture real regressions. Feature tests that cover the full HTTP path end-to-end are your best defence.',
                        'resources' => [
                            ['label' => 'Laravel Testing', 'url' => 'https://laravel.com/docs/testing'],
                            ['label' => 'Model Factories', 'url' => 'https://laravel.com/docs/eloquent-factories'],
                            ['label' => 'Pest PHP — modern testing', 'url' => 'https://pestphp.com/docs/installation'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Configure the test environment with SQLite in-memory in phpunit.xml'],
                            ['id' => 2, 'text' => 'Create factories with states: TaskFactory::pending(), TaskFactory::overdue()'],
                            ['id' => 3, 'text' => 'Write feature tests for: authentication, authorisation, and input validation'],
                            ['id' => 4, 'text' => 'Add a test that detects N+1 using `DB::getQueryLog()`'],
                            ['id' => 5, 'text' => 'Configure GitHub Actions to run tests on every PR'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // PATH 2 — Debugging Like a Pro
            // ─────────────────────────────────────────────────────────────
            [
                'name' => 'Debugging Like a Pro',
                'description' => 'The skill that most differentiates a junior from a senior is not writing code — it is finding where the code is wrong. This path teaches the scientific method applied to debugging: hypothesis, evidence, isolation, correction. Using Xdebug, structured logs and stack trace analysis as the main tools.',
                'steps' => [
                    [
                        'title' => 'Why Juniors Struggle to Debug (and How to Fix That)',
                        'type' => 'reading',
                        'description' => <<<'EOT'
                        The problem is not lack of effort — it is lack of method. Most junior devs add `var_dump` randomly until the bug disappears. This module introduces the scientific method of debugging: reproduce reliably, isolate variables, form a hypothesis, test, confirm. You will never debug in the dark again.

                        ## Two Approaches to the Same Bug

                        ```mermaid
                        flowchart TB
                            subgraph junior["❌  Junior: Random Search"]
                                J1(["🐛 Bug found"]):::red
                                J2["Add var_dump\nsomewhere"]:::red
                                J3["Run and check\noutput"]:::red
                                J4{"Bug gone?"}:::slate
                                J5(["🤞 Commit and hope"]):::red
                                J1 --> J2 --> J3 --> J4
                                J4 -->|No| J2
                                J4 -->|Yes| J5
                            end

                            subgraph senior["✅  Scientific Method"]
                                S1(["🐛 Bug found"]):::emerald
                                S2["Reproduce\nreliably"]:::emerald
                                S3["Isolate variables\n(binary search)"]:::emerald
                                S4["Form a\nhypothesis"]:::emerald
                                S5["Test with Xdebug\nor structured log"]:::emerald
                                S6{"Hypothesis\ncorrect?"}:::slate
                                S7["Fix + verify\nno regression"]:::emerald
                                S8(["✅ Commit with confidence"]):::emerald
                                S1 --> S2 --> S3 --> S4 --> S5 --> S6
                                S6 -->|No| S4
                                S6 -->|Yes| S7 --> S8
                            end

                            classDef emerald fill:#d1fae5,stroke:#059669,color:#065f46,font-weight:600
                            classDef slate   fill:#f1f5f9,stroke:#64748b,color:#1e293b,font-weight:500
                            classDef red     fill:#fee2e2,stroke:#ef4444,color:#991b1b,font-weight:500
                        ```

                        The junior loop is not wrong — it is just **unguided**. The scientific method forces you to think before acting, which is what separates a 30-minute fix from a 3-hour one.
                        EOT,
                        'resources' => [
                            ['label' => 'The Art of Debugging — pragmatic approach', 'url' => 'https://www.debuggingbook.org/'],
                            ['label' => 'Rubber Duck Debugging', 'url' => 'https://rubberduckdebugging.com/'],
                            ['label' => 'How to Debug — Julia Evans', 'url' => 'https://jvns.ca/blog/2022/12/21/new-zine--how-debugging-works/'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Xdebug: Setup, Breakpoints and Step Debugging',
                        'type' => 'lab',
                        'description' => '`var_dump` is the tool of someone who does not know Xdebug. With Xdebug and VS Code (or PhpStorm), you can pause execution at any line, inspect the full application state, and step through code line by line while watching variables change. This transforms debugging from guessing into investigation.',
                        'resources' => [
                            ['label' => 'Xdebug 3 — official docs', 'url' => 'https://xdebug.org/docs/step_debug'],
                            ['label' => 'VS Code PHP Debug extension', 'url' => 'https://marketplace.visualstudio.com/items?itemName=xdebug.php-debug'],
                            ['label' => 'Setting up Xdebug in Docker', 'url' => 'https://xdebug.org/docs/install'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Verify Xdebug is installed: `php -v` should show "with Xdebug v3"'],
                            ['id' => 2, 'text' => 'Configure VS Code: install the "PHP Debug" extension and create launch.json with port 9003'],
                            ['id' => 3, 'text' => 'Add a breakpoint on line 1 of a Laravel route and trigger it via curl'],
                            ['id' => 4, 'text' => 'Practice Step Over (F10), Step Into (F11), and Step Out (Shift+F11)'],
                            ['id' => 5, 'text' => 'Add a Watch Expression to monitor a specific variable'],
                            ['id' => 6, 'text' => 'Use Conditional Breakpoints: pause only when `$user->id === 5`'],
                            ['id' => 7, 'text' => 'Demonstrate full N+1 query debugging using Eloquent breakpoints'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Reading Stack Traces Like a Detective',
                        'type' => 'reading',
                        'description' => <<<'EOT'
                        A stack trace is a crime scene map — each line tells you where the system was and what it was doing when the error occurred. Most juniors read only the first line. In this module you will learn to read from bottom to top, identify your code frames vs vendor frames, understand chained exceptions, and extract the context that matters.

                        ## How an Exception Travels Through Your Call Stack

                        ```mermaid
                        sequenceDiagram
                            autonumber
                            participant C as Controller
                            participant S as UserService
                            participant R as UserRepository
                            participant DB as Database

                            Note over C, DB: A request arrives — the bug lives deep in the stack

                            C->>+S: getUser(id: 404)
                            S->>+R: find(id: 404)
                            R->>+DB: SELECT * FROM users WHERE id = 404
                            DB-->>-R: empty result set

                            rect rgb(254, 226, 226)
                                Note right of R: Frame 1 ← root cause
                                R-->>-S: throw ModelNotFoundException
                            end

                            rect rgb(241, 245, 249)
                                Note right of S: Frame 2
                                S-->>-C: exception propagates up
                            end

                            rect rgb(209, 250, 229)
                                Note right of C: Frame 3 ← top of stack trace
                                C-->>C: rendered as 500 response
                            end

                            Note over C, DB: Stack traces read bottom→top — Frame 1 is always the origin
                        ```

                        Your code frames are the ones under `app/`. Vendor frames (under `vendor/`) show you the path, but the bug is almost always in **your** code or in how you called the library.
                        EOT,
                        'resources' => [
                            ['label' => 'How to Read a Stack Trace', 'url' => 'https://rollbar.com/blog/php-stack-trace/'],
                            ['label' => 'PHP Exceptions — official docs', 'url' => 'https://www.php.net/manual/en/language.exceptions.php'],
                            ['label' => 'Ignition — Laravel error page explained', 'url' => 'https://flareapp.io/docs/ignition/introduction'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Structured Logging with Monolog and Laravel Log',
                        'type' => 'lab',
                        'description' => 'Logs are your application\'s memory in production — where Xdebug does not exist. Structured logs (JSON) are searchable, filterable and integrate with New Relic, Datadog, and any observability stack. We will configure separate channels by context, correct log levels, and context data that makes each log entry self-sufficient for investigation.',
                        'resources' => [
                            ['label' => 'Laravel Logging', 'url' => 'https://laravel.com/docs/logging'],
                            ['label' => 'Monolog documentation', 'url' => 'https://seldaek.github.io/monolog/'],
                            ['label' => 'Structured Logging best practices', 'url' => 'https://www.loggly.com/use-cases/best-practices-for-php-logging/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Configure a `structured` channel in config/logging.php with JSON formatter'],
                            ['id' => 2, 'text' => 'Add global context via `Log::withContext()` in middleware: user_id, request_id, ip'],
                            ['id' => 3, 'text' => 'Implement correct log levels: DEBUG for dev, INFO for user actions, ERROR for failures'],
                            ['id' => 4, 'text' => 'Create a custom Processor that adds `memory_usage` and `duration_ms` to each log'],
                            ['id' => 5, 'text' => 'Configure log rotation: daily logs with 30-day retention'],
                            ['id' => 6, 'text' => 'Simulate a production bug and resolve it using only the generated logs'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Challenge: Resolve the Incident Using Only Logs',
                        'type' => 'challenge',
                        'description' => 'In production you do not have Xdebug. You have logs. A payment system is failing silently — some payments are processed, others are not, and users only find out hours later. You have received a 48-hour log dump. Find the pattern, identify the root cause and propose the fix.',
                        'resources' => [
                            ['label' => 'Log analysis techniques', 'url' => 'https://www.elastic.co/what-is/log-analysis'],
                            ['label' => 'grep and awk for log analysis', 'url' => 'https://www.digitalocean.com/community/tutorials/how-to-use-journalctl-to-view-and-manipulate-systemd-logs'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Download the challenge log file (link in the materials)'],
                            ['id' => 2, 'text' => 'Use grep/awk to filter only payment events with ERROR status'],
                            ['id' => 3, 'text' => 'Identify the temporal pattern: does the bug happen always? Only at specific times?'],
                            ['id' => 4, 'text' => 'Correlate error logs with context logs (user_id, request_id) to trace a complete request'],
                            ['id' => 5, 'text' => 'Document your root cause hypothesis with evidence from the logs'],
                            ['id' => 6, 'text' => 'Write a test that reproduces the identified failure scenario'],
                        ],
                        'challenge_prompt' => 'The CFO called. 127 payments in the last 24 hours were marked as "pending" but the gateway already processed and charged the customer. The money left the user\'s account, but the order was never confirmed. You have the logs. You have 1 hour to find the cause before the board meeting.',
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Reproducing Production Bugs in a Local Environment',
                        'type' => 'reading',
                        'description' => 'The bug works in production but will not reproduce locally — the most frustrating phrase in development. We will cover the most common causes: data differences (seeds vs real production), timezone, race conditions, environment variables, and how to use sanitised database dumps, feature flags and Docker to bring your local environment closer to production reality.',
                        'resources' => [
                            ['label' => 'Reproducing production bugs locally', 'url' => 'https://blog.sentry.io/2020/06/24/what-is-a-reproduction/'],
                            ['label' => 'Database anonymisation tools', 'url' => 'https://github.com/machbarmacher/gdpr-dump'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Final Challenge: Debug Session — 3 Bugs, 60 Minutes',
                        'type' => 'challenge',
                        'description' => 'Three bugs of increasing complexity in a real Laravel application. You have Xdebug, the logs, and the source code. No hints. Document every step of your investigation — the process matters as much as the solution.',
                        'resources' => [
                            ['label' => 'PHP error reporting', 'url' => 'https://www.php.net/manual/en/function.error-reporting.php'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Bug #1 (Easy): An endpoint returns stale data — cache invalidation problem'],
                            ['id' => 2, 'text' => 'Bug #2 (Medium): Race condition in the stock reservation process'],
                            ['id' => 3, 'text' => 'Bug #3 (Hard): Memory leak that only appears after 1,000 requests in a job queue'],
                            ['id' => 4, 'text' => 'For each bug: document the hypothesis, the evidence that confirms it, and the fix'],
                            ['id' => 5, 'text' => 'Write a 1-paragraph post-mortem for each bug in the style of real incidents'],
                        ],
                        'challenge_prompt' => 'You have been promoted to the platform engineering team. Your first day: the incident queue has 3 bugs open for more than 72 hours that no one has been able to resolve. Your mission: close all three before end of day.',
                        'lab_url' => null,
                    ],
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // PATH 3 — APM with New Relic
            // ─────────────────────────────────────────────────────────────
            [
                'name' => 'APM with New Relic',
                'description' => 'Application Performance Monitoring transforms your application from a black box into a transparent system. With New Relic APM you can see every request, every query, every external call — and know exactly where time is being spent before users complain.',
                'steps' => [
                    [
                        'title' => 'What is APM and Why Every Developer Should Use It',
                        'type' => 'reading',
                        'description' => <<<'EOT'
                        APM (Application Performance Monitoring) is not just for ops — it is the tool that turns a developer into someone who truly understands the system they built. We will cover the concepts of traces, spans, throughput and error rate metrics, and why the 99th percentile matters more than the average.

                        ## What New Relic Sees From a Single Request

                        ```mermaid
                        flowchart LR
                            subgraph req["One HTTP Request"]
                                direction TB
                                T(["Trace · trace_id: abc123"]):::emerald
                                T --> S1["Span: GET /products\n45ms total"]:::slate
                                S1 --> S2["Span: Eloquent SELECT\n32ms"]:::slate
                                S1 --> S3["Span: Redis cache.get\n2ms"]:::slate
                                S1 --> S4["Span: JSON encode\n1ms"]:::slate
                            end

                            req -->|"aggregated across\nall requests"| DASH

                            subgraph DASH["APM Dashboard"]
                                M1["Throughput\n120 req/min"]:::blue
                                M2["Error Rate\n0.3%"]:::red
                                M3["p95 Latency\n380ms"]:::yellow
                                M4["Apdex Score\n0.94 / 1.0"]:::emerald
                            end

                            classDef emerald fill:#d1fae5,stroke:#059669,color:#065f46,font-weight:600
                            classDef slate   fill:#f1f5f9,stroke:#64748b,color:#1e293b,font-weight:500
                            classDef blue    fill:#dbeafe,stroke:#3b82f6,color:#1e40af,font-weight:500
                            classDef red     fill:#fee2e2,stroke:#ef4444,color:#991b1b,font-weight:500
                            classDef yellow  fill:#fef3c7,stroke:#f59e0b,color:#92400e,font-weight:500
                        ```

                        The 99th percentile (p99) matters more than the average because averages hide the outliers — and outliers are your most frustrated users.

                        ## Real-Time Incident Detection — What Happens When an Error Occurs

                        ```mermaid
                        sequenceDiagram
                            autonumber
                            participant U as 🌐 Browser
                            participant L as Laravel App
                            participant NR as New Relic Agent
                            participant DB as Database

                            Note over U, DB: A production request fails — APM captures everything

                            U->>+L: GET /api/checkout
                            L->>+DB: SELECT * FROM subscriptions WHERE user_id = ?
                            DB-->>-L: 504 Gateway Timeout

                            rect rgb(209, 250, 229)
                                Note right of L: Agent captures the failure automatically
                                L->>NR: newrelic_notice_error($exception)
                                L->>NR: newrelic_add_custom_parameter('plan', 'premium')
                                L->>NR: newrelic_add_custom_parameter('user_id', $user->id)
                            end

                            L-->>-U: HTTP 500 Internal Server Error

                            Note over NR, DB: Background telemetry export (non-blocking)
                            NR-->>NR: POST /v1/traces to New Relic Cloud
                        ```

                        Without APM, you find out about this failure when a user emails support. With APM, you get an alert in under 60 seconds — with the full trace, SQL query, and custom attributes already attached.
                        EOT,
                        'resources' => [
                            ['label' => 'New Relic APM Introduction', 'url' => 'https://docs.newrelic.com/docs/apm/new-relic-apm/getting-started/introduction-apm/'],
                            ['label' => 'Four Golden Signals — Google SRE', 'url' => 'https://sre.google/sre-book/monitoring-distributed-systems/'],
                            ['label' => 'APM vs Traditional Monitoring', 'url' => 'https://newrelic.com/resources/articles/what-is-apm'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Lab: Installing and Configuring the New Relic PHP Agent',
                        'type' => 'lab',
                        'description' => 'The New Relic PHP agent automatically instruments Laravel, Symfony, database frameworks and HTTP clients — without changing a single line of your code. In this lab you will install, configure and validate that data is reaching the platform.',
                        'resources' => [
                            ['label' => 'New Relic PHP Agent Install', 'url' => 'https://docs.newrelic.com/docs/apm/agents/php-agent/installation/php-agent-installation-overview/'],
                            ['label' => 'PHP Agent Configuration', 'url' => 'https://docs.newrelic.com/docs/apm/agents/php-agent/configuration/php-agent-configuration/'],
                            ['label' => 'New Relic Free Tier', 'url' => 'https://newrelic.com/signup'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Create a free account at newrelic.com and obtain your License Key'],
                            ['id' => 2, 'text' => 'Install the agent: `newrelic-install install` on the server (or via Docker in the Dockerfile)'],
                            ['id' => 3, 'text' => 'Configure newrelic.ini: app_name, license_key, and enabled=true'],
                            ['id' => 4, 'text' => 'Restart PHP-FPM and make 10 requests to your application'],
                            ['id' => 5, 'text' => 'Check in the New Relic panel > APM > your application: data should appear in ~2 minutes'],
                            ['id' => 6, 'text' => 'Navigate to Transactions and identify the slowest endpoint'],
                            ['id' => 7, 'text' => 'Open an individual trace and locate the SQL queries executed'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Reading Dashboards: Throughput, Error Rate and Apdex',
                        'type' => 'reading',
                        'description' => 'Having New Relic installed without knowing how to read the data is like having a stethoscope without knowing anatomy. We will dissect each section of the APM dashboard: what Apdex really measures, how to interpret error rate without panicking, the difference between average latency and p95/p99, and how Throughput per minute reveals real usage patterns.',
                        'resources' => [
                            ['label' => 'Apdex Score explained', 'url' => 'https://docs.newrelic.com/docs/apm/new-relic-apm/apdex/apdex-measure-user-satisfaction/'],
                            ['label' => 'APM Summary page walkthrough', 'url' => 'https://docs.newrelic.com/docs/apm/new-relic-apm/getting-started/summary-page/'],
                            ['label' => 'Understanding percentiles', 'url' => 'https://www.honeycomb.io/blog/percentiles-vs-averages'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Lab: Detecting and Fixing N+1 Queries with New Relic',
                        'type' => 'lab',
                        'description' => 'The N+1 is the most common performance bug in Laravel applications — and the most invisible without monitoring. New Relic shows exactly how many queries each request executes, with the full SQL and time spent. In this lab you will find real N+1s, confirm them with the Slow Query trace, and fix them with eager loading.',
                        'resources' => [
                            ['label' => 'N+1 Query Problem', 'url' => 'https://laravel.com/docs/eloquent-relationships#eager-loading'],
                            ['label' => 'New Relic Slow Query Traces', 'url' => 'https://docs.newrelic.com/docs/apm/new-relic-apm/getting-started/apm-agent-data-security/'],
                            ['label' => 'Laravel Debugbar', 'url' => 'https://github.com/barryvdh/laravel-debugbar'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Open New Relic > APM > Databases and sort by "Most time consuming"'],
                            ['id' => 2, 'text' => 'Click on the suspicious endpoint and open a Transaction Trace'],
                            ['id' => 3, 'text' => 'Identify repeated queries with a similar pattern (SELECT WHERE id = ?)'],
                            ['id' => 4, 'text' => 'Locate the corresponding Eloquent code and add eager loading `with()`'],
                            ['id' => 5, 'text' => 'Deploy and compare: the number of queries in the trace should drop from N to 1+1'],
                            ['id' => 6, 'text' => 'Configure an alert: notify if transaction time > 2s for more than 5 minutes'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Challenge: Your App is Slow — Find the Bottleneck in 30 Minutes',
                        'type' => 'challenge',
                        'description' => 'An e-commerce application has a p95 of 4.2 seconds on the product listing endpoint. Users are abandoning their carts. You have access to New Relic and the code. No infrastructure changes — the solution must be in the code.',
                        'resources' => [
                            ['label' => 'New Relic Transaction Traces', 'url' => 'https://docs.newrelic.com/docs/apm/transactions/transaction-traces/introduction-transaction-traces/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Access the challenge application\'s New Relic and navigate to APM > Transactions'],
                            ['id' => 2, 'text' => 'Identify the slowest endpoint and open the trace with the longest duration'],
                            ['id' => 3, 'text' => 'Note: how many queries were executed? How much time was spent in each segment?'],
                            ['id' => 4, 'text' => 'Locate the root cause in the code (could be N+1, unindexed query, or unnecessary synchronous processing)'],
                            ['id' => 5, 'text' => 'Implement the fix and document the expected improvement'],
                            ['id' => 6, 'text' => 'Write a 1-page report: problem found, evidence in New Relic, solution, result'],
                        ],
                        'challenge_prompt' => 'You are the on-call dev. It is Monday 9am and Black Friday is tomorrow. The SLA is p95 < 500ms but the product listing is at 4.2s. Marketing already sent 500k emails with the landing page link. You have New Relic open. What do you do first?',
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Errors Inbox: Triaging and Resolving Production Errors',
                        'type' => 'lab',
                        'description' => 'Errors Inbox is where production errors go to be investigated before they become incidents. We will configure error rate alerts, use Error Fingerprinting to group similar errors, analyse impact by number of affected users, and create a triage workflow that prevents silent errors from accumulating.',
                        'resources' => [
                            ['label' => 'New Relic Errors Inbox', 'url' => 'https://docs.newrelic.com/docs/errors-inbox/getting-started/'],
                            ['label' => 'Error alerting best practices', 'url' => 'https://docs.newrelic.com/docs/alerts-applied-intelligence/new-relic-alerts/alert-conditions/apm-metric-alert-conditions/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Navigate to New Relic > Errors Inbox and familiarise yourself with the automatic grouping'],
                            ['id' => 2, 'text' => 'Open the error with the highest impact (most affected users) and analyse the complete stack trace'],
                            ['id' => 3, 'text' => 'Create an Alert Policy: notify on Slack when error rate > 1% for 5 minutes'],
                            ['id' => 4, 'text' => 'Mark known errors as "Acknowledged" and configure owner + expected resolution'],
                            ['id' => 5, 'text' => 'Configure custom Error Fingerprinting for a specific error in your application'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Custom Instrumentation: Measuring What the Agent Does Not',
                        'type' => 'lab',
                        'description' => 'The automatic agent covers 80% of what you need. The other 20% — file processing, external API integrations, complex jobs — requires manual instrumentation. We will use the New Relic PHP API to create custom transactions, segments, and attributes that appear in traces.',
                        'resources' => [
                            ['label' => 'New Relic PHP API', 'url' => 'https://docs.newrelic.com/docs/apm/agents/php-agent/php-agent-api/guide-using-php-agent-api/'],
                            ['label' => 'Custom Attributes', 'url' => 'https://docs.newrelic.com/docs/apm/agents/php-agent/php-agent-api/newrelic_add_custom_parameter/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Add `newrelic_add_custom_parameter("user_plan", $user->plan)` to a critical endpoint'],
                            ['id' => 2, 'text' => 'Create a custom segment for an image processing block with `newrelic_start_transaction`'],
                            ['id' => 3, 'text' => 'Use `newrelic_notice_error()` to capture exceptions in a controlled way with additional context'],
                            ['id' => 4, 'text' => 'Verify in New Relic that the custom attributes appear in transaction details'],
                            ['id' => 5, 'text' => 'Create a NRQL query: `SELECT average(duration) FROM Transaction WHERE user_plan = \'premium\' SINCE 1 day ago`'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // PATH 4 — OpenTelemetry in Practice
            // ─────────────────────────────────────────────────────────────
            [
                'name' => 'OpenTelemetry in Practice',
                'description' => 'OpenTelemetry is the open standard for application instrumentation — vendor-neutral, portable across any observability backend. Learn to instrument PHP applications manually, create custom spans, and export data to New Relic via OTLP.',
                'steps' => [
                    [
                        'title' => 'OTel 101: Traces, Spans, Metrics and Logs',
                        'type' => 'reading',
                        'description' => <<<'EOT'
                        OpenTelemetry unifies the three pillars of observability — traces, metrics and logs — under a single API and SDK. We will understand the data model: what a Trace ID is, how Spans relate in parent-child relationships, the difference between gauge and counter metrics, and how W3C TraceContext propagates context between services.

                        ## The Three Pillars — and How OTel Connects Them

                        ```mermaid
                        flowchart TB
                            subgraph pillars["Three Pillars of Observability"]
                                direction LR
                                TRACES["📊 Traces\nFull journey of a request\nacross all services\n(parent → child spans)"]:::emerald
                                METRICS["📈 Metrics\nAggregated numbers:\nreq/sec · cpu · memory\nerror rate · p99"]:::blue
                                LOGS["📝 Logs\nStructured events\nwith context:\ntrace_id · span_id\nuser_id · severity"]:::yellow
                            end

                            SDK["OpenTelemetry SDK\nCollects all three pillars\nvia one unified API"]:::slate

                            pillars --> SDK
                            SDK --> OTLP["OTLP Exporter\n(gRPC or HTTP)"]:::slate
                            OTLP --> BACKEND["Any Observability Backend\nNew Relic · Datadog\nJaeger · Grafana Tempo"]:::emerald

                            classDef emerald fill:#d1fae5,stroke:#059669,color:#065f46,font-weight:600
                            classDef slate   fill:#f1f5f9,stroke:#64748b,color:#1e293b,font-weight:500
                            classDef blue    fill:#dbeafe,stroke:#3b82f6,color:#1e40af,font-weight:500
                            classDef yellow  fill:#fef3c7,stroke:#f59e0b,color:#92400e,font-weight:500
                        ```

                        Before OTel, you needed a different SDK for every backend. Now you instrument once and swap the exporter.
                        EOT,
                        'resources' => [
                            ['label' => 'OpenTelemetry Concepts', 'url' => 'https://opentelemetry.io/docs/concepts/'],
                            ['label' => 'Observability Primer', 'url' => 'https://opentelemetry.io/docs/concepts/observability-primer/'],
                            ['label' => 'W3C Trace Context spec', 'url' => 'https://www.w3.org/TR/trace-context/'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Lab: Installing the OpenTelemetry PHP SDK',
                        'type' => 'lab',
                        'description' => 'The OpenTelemetry PHP SDK is stable and production-ready. We will install it via Composer, configure the Tracer Provider with an OTLP exporter, and validate that spans reach the backend. Correctly configuring auto-instrumentation for Laravel is the most important step.',
                        'resources' => [
                            ['label' => 'OpenTelemetry PHP SDK', 'url' => 'https://opentelemetry.io/docs/languages/php/'],
                            ['label' => 'opentelemetry-php on GitHub', 'url' => 'https://github.com/open-telemetry/opentelemetry-php'],
                            ['label' => 'New Relic OTLP endpoint', 'url' => 'https://docs.newrelic.com/docs/opentelemetry/best-practices/opentelemetry-otlp/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Install: `composer require open-telemetry/sdk open-telemetry/exporter-otlp`'],
                            ['id' => 2, 'text' => 'Install the PHP extension: `pecl install opentelemetry` and enable it in php.ini'],
                            ['id' => 3, 'text' => 'Install auto-instrumentation for Laravel: `composer require open-telemetry/opentelemetry-auto-laravel`'],
                            ['id' => 4, 'text' => 'Configure via env vars: OTEL_SERVICE_NAME, OTEL_EXPORTER_OTLP_ENDPOINT, OTEL_EXPORTER_OTLP_HEADERS'],
                            ['id' => 5, 'text' => 'Run `php artisan serve` and make a request — it should appear in the observability backend'],
                            ['id' => 6, 'text' => 'Verify: does the trace have spans for HTTP request, middleware, controller, and SQL queries?'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Manual Instrumentation: Creating Custom Spans',
                        'type' => 'lab',
                        'description' => 'Auto-instrumentation covers frameworks and libraries. For your own business logic — checkout processes, score calculations, proprietary integrations — you need to create spans manually. We will cover how to create child spans, add semantic attributes, record events within spans, and mark spans as errors with the correct status.',
                        'resources' => [
                            ['label' => 'Creating Spans manually', 'url' => 'https://opentelemetry.io/docs/languages/php/instrumentation/'],
                            ['label' => 'Semantic Conventions', 'url' => 'https://opentelemetry.io/docs/specs/semconv/'],
                            ['label' => 'OTel PHP examples', 'url' => 'https://github.com/open-telemetry/opentelemetry-php/tree/main/examples'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Inject the Tracer into your Service via constructor: `OpenTelemetry\\API\\Globals::tracerProvider()->getTracer(\'my-service\')`'],
                            ['id' => 2, 'text' => 'Create a span for the checkout process: `$span = $tracer->spanBuilder(\'checkout.process\')->startSpan()`'],
                            ['id' => 3, 'text' => 'Add attributes: `$span->setAttribute(\'checkout.total\', $total)->setAttribute(\'checkout.items\', count($items))`'],
                            ['id' => 4, 'text' => 'Create child spans for each step: validation, shipping calculation, payment processing'],
                            ['id' => 5, 'text' => 'On exception: `$span->recordException($e)->setStatus(StatusCode::STATUS_ERROR, $e->getMessage())`'],
                            ['id' => 6, 'text' => 'Always finalise with `$span->end()` in the finally block'],
                            ['id' => 7, 'text' => 'Visualise the complete trace in the backend and confirm the parent-child span hierarchy'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Exporting to New Relic via OTLP',
                        'type' => 'lab',
                        'description' => 'New Relic is a native OTLP backend — it accepts traces, metrics and logs via OTLP/gRPC or OTLP/HTTP. We will correctly configure the exporter, understand the authentication headers, and validate that traces created with OTel appear in New Relic APM alongside data from the native agent.',
                        'resources' => [
                            ['label' => 'New Relic OTLP ingest', 'url' => 'https://docs.newrelic.com/docs/opentelemetry/best-practices/opentelemetry-otlp/'],
                            ['label' => 'OTLP Specification', 'url' => 'https://opentelemetry.io/docs/specs/otlp/'],
                            ['label' => 'NR OpenTelemetry examples', 'url' => 'https://github.com/newrelic/newrelic-opentelemetry-examples'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Configure OTEL_EXPORTER_OTLP_ENDPOINT=https://otlp.nr-data.net:4317 (or 443 for HTTPS)'],
                            ['id' => 2, 'text' => 'Add auth header: OTEL_EXPORTER_OTLP_HEADERS=api-key=YOUR_NEW_RELIC_LICENSE_KEY'],
                            ['id' => 3, 'text' => 'Configure resource attributes: OTEL_RESOURCE_ATTRIBUTES=service.name=my-app,environment=production'],
                            ['id' => 4, 'text' => 'Test the connection: make requests and check in NR > APM > Services > OpenTelemetry'],
                            ['id' => 5, 'text' => 'Correlate an OTel trace with a native PHP agent trace — NR automatically links them by trace ID'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Correlating Logs with Traces',
                        'type' => 'lab',
                        'description' => 'Logs without trace context are hard to correlate with the problem that caused them. When you inject trace_id and span_id into logs, New Relic (and any OTel backend) automatically connects each log entry to the corresponding trace. We will configure Monolog to automatically inject OTel context into each log entry.',
                        'resources' => [
                            ['label' => 'Logs in Context — OTel', 'url' => 'https://opentelemetry.io/docs/specs/otel/logs/'],
                            ['label' => 'NR Logs in Context for PHP', 'url' => 'https://docs.newrelic.com/docs/logs/logs-context/configure-logs-context-php/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Add the OTel-Monolog bridge: `composer require open-telemetry/opentelemetry-logger-monolog`'],
                            ['id' => 2, 'text' => 'Configure the Processor that automatically injects trace_id and span_id into each log record'],
                            ['id' => 3, 'text' => 'Use JSON format in the production channel'],
                            ['id' => 4, 'text' => 'Make a request that generates logs and an error — see in New Relic the log linked to the trace'],
                            ['id' => 5, 'text' => 'Navigate: New Relic Trace > span with error > "See logs" — should open filtered by trace_id'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Challenge: Instrument This API End-to-End',
                        'type' => 'challenge',
                        'description' => 'An order processing API has no instrumentation at all. You need to add OTel from scratch: auto-instrumentation, custom spans in business services, log correlation, and export to New Relic. The goal is that any production error is traceable end-to-end in under 2 minutes.',
                        'resources' => [
                            ['label' => 'OTel PHP complete guide', 'url' => 'https://opentelemetry.io/docs/languages/php/getting-started/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Clone the non-instrumented API repository (link in the challenge materials)'],
                            ['id' => 2, 'text' => 'Install and configure the OTel SDK with auto-instrumentation for Laravel'],
                            ['id' => 3, 'text' => 'Add manual spans in services: OrderService, PaymentService, NotificationService'],
                            ['id' => 4, 'text' => 'Configure log correlation with trace_id/span_id via Monolog processor'],
                            ['id' => 5, 'text' => 'Configure export to New Relic via OTLP'],
                            ['id' => 6, 'text' => 'Simulate an error in PaymentService and demonstrate: find the error in NR Errors Inbox, open the trace, navigate to correlated logs — all in under 2 minutes'],
                            ['id' => 7, 'text' => 'Document the setup in an instrumentation README.md'],
                        ],
                        'challenge_prompt' => 'You have just joined a company that has never instrumented its applications. Your first task from your tech lead: "instrument this API before tomorrow\'s launch — if there\'s a problem in production, I need to know in seconds, not hours." Show the result.',
                        'lab_url' => null,
                    ],
                ],
            ],

            // ─────────────────────────────────────────────────────────────
            // PATH 5 — Full Stack Observability
            // ─────────────────────────────────────────────────────────────
            [
                'name' => 'Full Stack Observability',
                'description' => 'The complete picture: from the user\'s browser click to the database query. Learn to instrument the frontend with the Browser Agent, correlate frontend and backend traces, create custom dashboards for your product, and respond to incidents in minutes with concrete evidence.',
                'steps' => [
                    [
                        'title' => 'Browser Monitoring: Installing the New Relic JS Agent',
                        'type' => 'lab',
                        'description' => 'The backend can be perfect and users can still have a terrible experience — due to network issues, heavy JavaScript, or slow APIs. New Relic Browser Monitor captures real user performance (RUM), Core Web Vitals, JS errors, and AJAX calls with real timings from each country and device.',
                        'resources' => [
                            ['label' => 'New Relic Browser Agent', 'url' => 'https://docs.newrelic.com/docs/browser/browser-monitoring/getting-started/introduction-browser-monitoring/'],
                            ['label' => 'Core Web Vitals', 'url' => 'https://web.dev/vitals/'],
                            ['label' => 'Browser monitoring copy/paste install', 'url' => 'https://docs.newrelic.com/docs/browser/browser-monitoring/installation/install-browser-monitoring-agent/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'In New Relic, navigate to Browser > Add data and choose the copy/paste method'],
                            ['id' => 2, 'text' => 'Add the JS snippet in the <head> of your Nuxt layout via useHead() or a plugin'],
                            ['id' => 3, 'text' => 'Configure the correct applicationID for the environment (dev vs prod)'],
                            ['id' => 4, 'text' => 'Access your application on different pages and verify in NR > Browser that pageviews appear'],
                            ['id' => 5, 'text' => 'Navigate to Core Web Vitals and analyse LCP, FID/INP and CLS for your application'],
                            ['id' => 6, 'text' => 'Identify the heaviest resource in the "Session traces" tab'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Core Web Vitals and Frontend Performance',
                        'type' => 'reading',
                        'description' => 'Google uses Core Web Vitals in search rankings, and users abandon pages that take more than 3 seconds. LCP (Largest Contentful Paint), FID/INP (Interaction to Next Paint) and CLS (Cumulative Layout Shift) are the three metrics that measure real experience. We will understand what each one measures, what the acceptable thresholds are, and which optimisations have the greatest impact.',
                        'resources' => [
                            ['label' => 'Web Vitals — web.dev', 'url' => 'https://web.dev/vitals/'],
                            ['label' => 'LCP optimisation guide', 'url' => 'https://web.dev/optimize-lcp/'],
                            ['label' => 'New Relic Core Web Vitals', 'url' => 'https://docs.newrelic.com/docs/browser/browser-monitoring/page-load-timing-resources/pageviewtiming-async-or-dynamic-page-details/'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Distributed Tracing: From Click to Database',
                        'type' => 'reading',
                        'description' => <<<'EOT'
                        In a modern system, a single user action triggers requests to multiple services. Without distributed tracing, you only see the symptom (slowness or error) without knowing where it occurred. We will understand how trace context propagates via HTTP headers (W3C TraceContext) and how to interpret a trace waterfall.

                        ## From Browser Click to Database — One Trace

                        ```mermaid
                        flowchart LR
                            subgraph browser["Browser"]
                                B1["User clicks\n'Confirm Order'"]:::emerald
                                B2["POST /api/orders\ntraceparent injected\nby NR Browser Agent"]:::emerald
                                B1 --> B2
                            end

                            subgraph backend["Laravel Backend"]
                                C1["HTTP Middleware\nextracts traceparent\ncontinues the trace"]:::slate
                                C2["OrderController\nspan: order.create · 210ms"]:::emerald
                                C3["PaymentService\nspan: payment.charge · 180ms"]:::emerald
                                C4["DB INSERT orders\nspan: db.query · 4ms"]:::slate
                                C5["NotifyService\nspan: email.send · 22ms"]:::slate
                                C1 --> C2
                                C2 --> C3
                                C2 --> C4
                                C2 --> C5
                            end

                            subgraph ext["External APIs"]
                                E1["Stripe"]:::blue
                                E2["Mailgun"]:::blue
                            end

                            B2 --> C1
                            C3 --> E1
                            C5 --> E2

                            TRACE(["🔍 All spans share\nthe same trace_id\nOne unified view\nfrom click to DB"]):::emerald
                            C2 -.->|"NR links\nautomatically"| TRACE

                            classDef emerald fill:#d1fae5,stroke:#059669,color:#065f46,font-weight:600
                            classDef slate   fill:#f1f5f9,stroke:#64748b,color:#1e293b,font-weight:500
                            classDef blue    fill:#dbeafe,stroke:#3b82f6,color:#1e40af,font-weight:500
                        ```

                        Without distributed tracing, a 4-second response is a mystery. With it, you see immediately that 3.8 seconds were spent waiting for Stripe.
                        EOT,
                        'resources' => [
                            ['label' => 'New Relic Distributed Tracing', 'url' => 'https://docs.newrelic.com/docs/distributed-tracing/concepts/introduction-distributed-tracing/'],
                            ['label' => 'W3C TraceContext propagation', 'url' => 'https://www.w3.org/TR/trace-context/'],
                            ['label' => 'Tracing vs Logging vs Metrics', 'url' => 'https://peter.bourgon.org/blog/2017/02/21/metrics-tracing-and-logging.html'],
                        ],
                        'instructions' => null,
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Lab: Logs in Context in New Relic',
                        'type' => 'lab',
                        'description' => 'Logs in Context is the feature that connects a log entry directly to the trace and span that generated it — without manually searching by timestamps or user IDs. We will configure the PHP agent to automatically inject linking metadata into logs, and use NR Logs to navigate from a production error to the full trace in one click.',
                        'resources' => [
                            ['label' => 'PHP Logs in Context', 'url' => 'https://docs.newrelic.com/docs/logs/logs-context/configure-logs-context-php/'],
                            ['label' => 'NR Log Management', 'url' => 'https://docs.newrelic.com/docs/logs/get-started/get-started-log-management/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Install the New Relic monolog handler: `composer require newrelic/monolog-enricher`'],
                            ['id' => 2, 'text' => 'Configure the Processor and Handler in config/logging.php in the production channel'],
                            ['id' => 3, 'text' => 'Ensure the log format is JSON with the fields: trace.id, span.id, entity.guid'],
                            ['id' => 4, 'text' => 'Configure the NR agent to forward logs: newrelic.application_logging.forwarding.enabled=true'],
                            ['id' => 5, 'text' => 'Simulate an error: open NR > APM > Errors > click the error > "See logs" — should open filtered by trace_id'],
                            ['id' => 6, 'text' => 'Navigate the reverse path: New Relic Logs > filter by trace.id= > open the associated trace'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Creating Custom Dashboards with NRQL',
                        'type' => 'lab',
                        'description' => 'Dashboards are the difference between reacting to incidents and predicting problems. NRQL (New Relic Query Language) is SQL for telemetry — it allows you to aggregate any collected data into custom visualisations for your specific product. We will create an SLI/SLO dashboard with the metrics that matter for your business.',
                        'resources' => [
                            ['label' => 'NRQL Reference', 'url' => 'https://docs.newrelic.com/docs/nrql/nrql-syntax-clauses-functions/'],
                            ['label' => 'Dashboard best practices', 'url' => 'https://docs.newrelic.com/docs/query-your-data/explore-query-data/dashboards/introduction-dashboards/'],
                            ['label' => 'SLI/SLO with New Relic', 'url' => 'https://docs.newrelic.com/docs/service-level-management/intro-slm/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Create a new dashboard: New Relic > Dashboards > Create dashboard'],
                            ['id' => 2, 'text' => 'Widget 1 — Availability: `SELECT percentage(count(*), WHERE error IS false) FROM Transaction SINCE 24 hours ago`'],
                            ['id' => 3, 'text' => 'Widget 2 — p95 Latency: `SELECT percentile(duration, 95) FROM Transaction FACET name SINCE 1 hour ago TIMESERIES`'],
                            ['id' => 4, 'text' => 'Widget 3 — Error Rate: `SELECT percentage(count(*), WHERE error IS true) FROM Transaction TIMESERIES AUTO`'],
                            ['id' => 5, 'text' => 'Widget 4 — Top Slow Queries: `SELECT average(duration) FROM DatabaseOperation FACET statement LIMIT 10`'],
                            ['id' => 6, 'text' => 'Configure alerts linked to the dashboard: notify if availability < 99.5% for 10 minutes'],
                        ],
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Challenge: Trace a Bug from Frontend to Database',
                        'type' => 'challenge',
                        'description' => 'A user reported: "I clicked Confirm Order, the screen spun for 10 seconds and vanished. Nothing happened." You have New Relic Browser + APM + Logs configured. Without reproducing the bug — use only the telemetry collected during the user\'s report to find what happened.',
                        'resources' => [
                            ['label' => 'New Relic Session Replay', 'url' => 'https://docs.newrelic.com/docs/browser/browser-monitoring/browser-pro-features/session-replay/'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Access NR Browser > Session Traces and filter by the user_id of the reporting user'],
                            ['id' => 2, 'text' => 'Identify the exact moment of "Confirm Order" via AJAX call in the Session Trace'],
                            ['id' => 3, 'text' => 'Copy the trace_id of that request and open it in NR Distributed Tracing'],
                            ['id' => 4, 'text' => 'In the trace, identify which span failed (status ERROR) and in which service'],
                            ['id' => 5, 'text' => 'Navigate to Logs filtered by that trace_id — what is the full error message?'],
                            ['id' => 6, 'text' => 'Locate the exact failure point in the code and propose the fix'],
                            ['id' => 7, 'text' => 'Write a 1-page incident report: timeline, root cause, impact, fix, future prevention'],
                        ],
                        'challenge_prompt' => 'An enterprise client is threatening to cancel their contract. The bug happened 3 times during the demo with their team. You cannot reproduce it locally. But you have complete observability of the real event. Find it, explain it and fix it before the follow-up meeting tomorrow at 9am.',
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Final Challenge: Simulate a Complete Incident Response',
                        'type' => 'challenge',
                        'description' => 'A P1 incident has been declared. Error rate jumped from 0.1% to 23% in 4 minutes. Alerts fired. You are the incident commander. Lead the investigation, coordinate communication, and deliver the post-mortem. This is the closest training to the reality of an SRE or senior developer in production.',
                        'resources' => [
                            ['label' => 'Google SRE Incident Management', 'url' => 'https://sre.google/sre-book/managing-incidents/'],
                            ['label' => 'Post-mortem Culture', 'url' => 'https://sre.google/sre-book/postmortem-culture/'],
                            ['label' => 'Incident Response Checklist', 'url' => 'https://github.com/dastergon/awesome-sre#incident-management'],
                        ],
                        'instructions' => [
                            ['id' => 1, 'text' => 'Step 1 — Triage (first 5 min): open NR, identify which endpoints are erroring and the user impact'],
                            ['id' => 2, 'text' => 'Step 2 — Hypothesis (5-10 min): correlate the spike\'s start with recent deploys (NR Change Tracking)'],
                            ['id' => 3, 'text' => 'Step 3 — Containment (10-15 min): if the issue is a deploy, execute rollback; if it\'s data, apply a hotpath fix'],
                            ['id' => 4, 'text' => 'Step 4 — Validation (15-20 min): confirm in NR that error rate returned to normal and metrics stabilised'],
                            ['id' => 5, 'text' => 'Step 5 — Post-mortem (after resolution): write the post-mortem with: timeline, root cause, impact, corrective and preventive actions'],
                            ['id' => 6, 'text' => 'Deliver the post-mortem in blameless format — focus on the system, not the people'],
                        ],
                        'challenge_prompt' => 'It is 2:37pm on a Friday. Slack exploded: "PROD DOWN - checkout 500s". The CEO is travelling but messaged the group. The support team reports 847 tickets opened in the last 4 minutes. You have New Relic open. Start.',
                        'lab_url' => null,
                    ],
                ],
            ],
        ];
    }
}
