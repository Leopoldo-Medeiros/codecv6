---json
{
    "order": 7,
    "title": "Testing Mindset: Tests That Actually Protect",
    "type": "reading",
    "description": "Poorly written tests give false confidence and slow down development. We cover the difference between unit, feature and integration tests in Laravel, when to mock vs hit a real database, factory states, the N+1 regression detector, and what to *not* test.",
    "tldr": "A test suite's value isn't the green checkmark — it's the bugs it catches at 3am before a customer does. Feature tests covering real HTTP paths are your highest-leverage tool; mocks should be the exception, not the default; and the most important assertion is the one you skip when you're tired (the negative case).",
    "difficulty": "intermediate",
    "estimated_minutes": 26,
    "challenge_slug": null,
    "lab_url": null,
    "has_playground": true,
    "playground_starter_code": "<?php\ndeclare(strict_types=1);\n\n// A Laravel feature test that does the three things every test should do:\n//   1. ARRANGE — build the world the test needs (factories with state).\n//   2. ACT     — perform one action.\n//   3. ASSERT  — check exactly the consequences you care about.\n//\n// In a real project this lives in tests/Feature/Task/CreateTaskTest.php.\n\nuse Illuminate\\Foundation\\Testing\\RefreshDatabase;\nuse Tests\\TestCase;\n\nclass CreateTaskTest extends TestCase\n{\n    use RefreshDatabase;\n\n    public function test_authenticated_user_can_create_a_task(): void\n    {\n        // ── ARRANGE\n        $user = \\App\\Models\\User::factory()->create();\n\n        // ── ACT\n        $response = $this->actingAs($user)->postJson('/api/tasks', [\n            'title' => 'Write the test before the code',\n            'due_at' => now()->addDay()->toIsoString(),\n        ]);\n\n        // ── ASSERT — three layers: HTTP, DB, and shape of returned data\n        $response->assertCreated();\n        $this->assertDatabaseHas('tasks', [\n            'user_id' => $user->id,\n            'title'   => 'Write the test before the code',\n        ]);\n        $response->assertJsonPath('data.user_id', $user->id);\n    }\n}",
    "challenge_prompt": null,
    "resources": [
        {
            "url": "https://laravel.com/docs/testing",
            "label": "Laravel Testing"
        },
        {
            "url": "https://laravel.com/docs/eloquent-factories",
            "label": "Model Factories"
        },
        {
            "url": "https://pestphp.com/docs/installation",
            "label": "Pest PHP — modern testing"
        },
        {
            "url": "https://martinfowler.com/articles/mocksArentStubs.html",
            "label": "Mocks aren't stubs (Martin Fowler)"
        }
    ],
    "instructions": [
        {
            "id": 1,
            "text": "Configure the test environment — set DB_CONNECTION=sqlite and DB_DATABASE=:memory: in phpunit.xml. Time a 10-test run before and after. The difference is what you''ll save on every CI build for the rest of the project''s life.",
            "starter_code": null
        },
        {
            "id": 2,
            "text": "Build factories with states — implement TaskFactory with pending() and overdue() states (overdue = due_at < now AND status != done). Use the states in a feature test that asserts the /api/tasks/overdue endpoint returns exactly the overdue rows.",
            "starter_code": "<?php\ndeclare(strict_types=1);\n\nnamespace Database\\Factories;\n\nuse App\\Models\\Task;\nuse App\\Models\\User;\nuse Illuminate\\Database\\Eloquent\\Factories\\Factory;\n\nclass TaskFactory extends Factory\n{\n    protected $model = Task::class;\n\n    public function definition(): array\n    {\n        return [\n            'user_id' => User::factory(),\n            'title'   => $this->faker->sentence(),\n            'status'  => 'pending',\n            'due_at'  => now()->addDays(7),\n        ];\n    }\n\n    public function pending(): self\n    {\n        // TODO: state for explicitly-pending tasks (status = 'pending').\n    }\n\n    public function overdue(): self\n    {\n        // TODO: state for overdue tasks (due_at in the past AND status != 'done').\n    }\n}"
        },
        {
            "id": 3,
            "text": "Write the three feature tests that protect login: success, wrong password (returns 422), and throttled after five failed attempts (returns 429). Junior tests cover (1); the bugs that ship are (2) and (3).",
            "starter_code": "<?php\ndeclare(strict_types=1);\n\nnamespace Tests\\Feature\\Auth;\n\nuse App\\Models\\User;\nuse Illuminate\\Foundation\\Testing\\RefreshDatabase;\nuse Illuminate\\Support\\Facades\\Hash;\nuse Tests\\TestCase;\n\nclass LoginTest extends TestCase\n{\n    use RefreshDatabase;\n\n    public function test_user_can_log_in_with_valid_credentials(): void\n    {\n        // ARRANGE: create a user with a known password\n        // ACT:     post valid credentials to /api/login\n        // ASSERT:  200, response carries an access_token, DB shows last_login bumped\n    }\n\n    public function test_login_rejects_wrong_password(): void\n    {\n        // TODO: 422 + no token in response + DB unchanged\n    }\n\n    public function test_login_throttles_after_five_failed_attempts(): void\n    {\n        // TODO: 5 failed posts, then the 6th returns 429\n    }\n}"
        },
        {
            "id": 4,
            "text": "Add an N+1 detector — implement the AssertsQueryCount trait from the Deeper dive section. Use it in a feature test for /api/orders that creates 20 orders and asserts the index endpoint runs at most 3 queries. The 4th query that future-you adds for a relation will fail the test instead of fail in prod.",
            "starter_code": "<?php\ndeclare(strict_types=1);\n\nnamespace Tests\\Traits;\n\nuse Illuminate\\Support\\Facades\\DB;\n\ntrait AssertsQueryCount\n{\n    protected function assertQueryCountAtMost(int $max, callable $action): void\n    {\n        DB::flushQueryLog();\n        DB::enableQueryLog();\n\n        $action();\n\n        // TODO: count DB::getQueryLog() and assertLessThanOrEqual($max, $count)\n        // with a helpful message including the actual count.\n    }\n}\n\n// Usage in tests/Feature/Order/IndexTest.php:\n//\n// use AssertsQueryCount;\n//\n// public function test_index_does_not_n_plus_one(): void\n// {\n//     Order::factory()->for(User::factory())->count(20)->create();\n//\n//     $this->assertQueryCountAtMost(3, function () {\n//         $this->getJson('/api/orders')->assertOk();\n//     });\n// }"
        },
        {
            "id": 5,
            "text": "Senior interview drill — write a three-sentence answer to \"What''s your testing strategy?\" *without* mentioning coverage percentage. If you can''t avoid the word coverage, you''re measuring the wrong thing and an interviewer will catch it.",
            "starter_code": null
        }
    ],
    "prerequisites": [
        {
            "id": 1,
            "title": "Laravel Request Lifecycle"
        },
        {
            "id": 2,
            "title": "Advanced Eloquent"
        },
        {
            "id": 3,
            "title": "Code Review: What a Senior Would See"
        }
    ],
    "concepts": [
        "feature-tests",
        "unit-tests",
        "factories",
        "states",
        "mocks-vs-fakes",
        "n+1-detector",
        "refresh-database",
        "tdd"
    ],
    "quiz": null,
    "evidence": null
}
---
## Core (foundations)

### The test pyramid in a Laravel app

Three layers, in order of cost-vs-confidence:

| Layer        | What it tests                                | Speed | Use when…                                              |
| ------------ | -------------------------------------------- | ----- | ------------------------------------------------------ |
| **Unit**     | One class/function in isolation              | ms    | The thing has clear inputs and outputs and no I/O.     |
| **Feature**  | A full HTTP request → response               | tens of ms | The thing crosses controller, service, DB, validation. |
| **Integration** | Multiple services together (queue, mail) | seconds | You''re testing an end-to-end workflow.              |

Most Laravel apps lean too far into unit tests. **Feature tests are where the bugs hide** — they exercise routing, middleware, validation, DB, response shape, all in one go. A feature test that fails after a refactor is worth ten unit tests that all pass.

### The Arrange / Act / Assert structure

Every test has three sections, in order:

```php
public function test_orders_can_be_filtered_by_status(): void
{
    // ── ARRANGE: build only what this test needs
    $user = User::factory()->create();
    Order::factory()->for($user)->paid()->count(3)->create();
    Order::factory()->for($user)->refunded()->count(2)->create();

    // ── ACT: one action — usually one HTTP call or one method call
    $response = $this->actingAs($user)->getJson('/api/orders?status=paid');

    // ── ASSERT: prove the consequence
    $response->assertOk();
    $this->assertCount(3, $response->json('data'));
}
```

If your test has two ACT blocks, it''s two tests. If your test has zero ASSERT calls, it''s a coverage decoration — passing it tells you nothing.

### Factories and states

Factories are your tests'' world-builder. Without them every test starts with 15 lines of `Model::create([...])`. With them, the test reads as a sentence.

```php
class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'total_cents' => 9999,
            'status' => 'pending',
        ];
    }

    // States — call them after factory() to vary the world.
    public function paid(): self
    {
        return $this->state(fn () => ['status' => 'paid', 'paid_at' => now()]);
    }

    public function overdue(): self
    {
        return $this->state(fn () => [
            'due_at' => now()->subWeek(),
            'status' => 'pending',
        ]);
    }
}

// Usage:
Order::factory()->paid()->count(5)->for($user)->create();
Order::factory()->overdue()->create();
```

States are the single biggest win you can give your test suite. They turn "create a paid order due last week" from a 6-line block into a one-liner.

### RefreshDatabase and the in-memory SQLite trick

Two things make a Laravel test suite fast:

1. The `RefreshDatabase` trait runs each test inside a transaction that''s rolled back at the end. The DB state from test A doesn''t leak into test B.
2. Pointing the test DB at SQLite in-memory: every test starts with a clean migrated schema in milliseconds.

```xml
<!-- phpunit.xml -->
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

If your CI takes 5 minutes per push, half of that is probably the test DB. Switching to in-memory SQLite can drop a 400-test suite from 4 minutes to 40 seconds. *Caveat:* SQLite isn''t MySQL. If your code uses MySQL-specific SQL (window functions, JSON_TABLE), you have to run those tests against MySQL — a separate, slower lane in CI.

## Deeper dive (intermediate)

### Mocks, fakes, spies — and when to use which

These three things are not synonyms. Knowing which to reach for is what separates a clean test suite from a brittle one.

| Tool       | What it does                          | Use when…                                                       |
| ---------- | ------------------------------------- | --------------------------------------------------------------- |
| **Fake**   | A real, in-memory implementation      | The dependency is owned by the framework (`Mail::fake()`, `Queue::fake()`, `Storage::fake()`). |
| **Mock**  | Specifies exact expected calls         | You need to assert "this method was called with these args". Rare. |
| **Spy**    | Records calls without specifying them | You want to check after the fact what happened. Less brittle than a mock. |

**Reach for fakes first.** Laravel ships them for Mail, Queue, Storage, Notification, Bus, Event. They give you real assertions (`Mail::assertSent(InvoiceMail::class)`) without testing implementation details.

Mocks are the test smell of last resort. Every `->shouldReceive(...)->with(...)->once()` is a coupling between your test and your code. Refactor the production code and the test fails for no reason.

### Testing the unhappy paths

Junior tests look like this:

```php
public function test_user_can_log_in(): void
{
    $user = User::factory()->create(['password' => Hash::make('secret')]);
    $this->postJson('/api/login', ['email' => $user->email, 'password' => 'secret'])
        ->assertOk();
}
```

The bug lives in the path this test doesn''t check. Add these — they''re the regressions you actually ship:

```php
public function test_login_fails_with_wrong_password(): void { /* ... */ }
public function test_login_throttles_after_five_failed_attempts(): void { /* ... */ }
public function test_login_locks_account_for_an_hour_after_ten_failures(): void { /* ... */ }
```

For every happy-path test, ask: *"What''s the bug we''d ship if I forget to handle this branch?"* — and write the test that would catch it.

### The N+1 regression detector

N+1 bugs love hiding behind passing tests. Catch them with a custom assertion:

```php
trait AssertsQueryCount
{
    protected function assertQueryCountAtMost(int $max, callable $action): void
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $action();

        $queries = count(DB::getQueryLog());
        $this->assertLessThanOrEqual(
            $max,
            $queries,
            "Expected ≤ {$max} queries, got {$queries}."
        );
    }
}

// In your feature test:
public function test_index_does_not_n_plus_one(): void
{
    Order::factory()->for(User::factory())->count(20)->create();

    $this->assertQueryCountAtMost(3, function () {
        $this->actingAs(User::factory()->create())->getJson('/api/orders');
    });
}
```

The threshold is empirical: run the test, see how many queries it takes today, set the bound at that number. The next dev who adds an N+1 will see a *test failure*, not a Datadog alert at 11pm.

### TDD as a design tool (not a religion)

TDD = write the test first. The reason isn''t purity — it''s that writing the test forces you to commit to a contract before you implement. You decide what the function''s name is, what its inputs are, what its output should look like — before you can lose those decisions in implementation details.

In practice, the move is **TDD for the public-facing contract, regular development for the leaves**. Write the feature test that says "POST /api/orders creates an Order and returns its id". Then implement until it passes. Inside the controller, you can move freely; you have an executable specification on top.

If you find yourself writing tests *after* the code, that''s fine — just make sure the test would fail without your change. Comment out the implementation, watch the test go red, uncomment.

## Senior insights (testing strategy)

### What to NOT test

The senior trap isn''t writing too few tests — it''s writing too many of the wrong ones. Things to skip:

- **Framework code.** Don''t test that Laravel''s validator validates. Don''t test that Eloquent saves to the DB.
- **Trivial getters and setters.** `$user->name = 'Ada'; $this->assertSame('Ada', $user->name);` is a coverage tax.
- **Behaviour that''s really configuration.** If a test is "did I set this config value correctly", that''s not a test — that''s reading `config/app.php`.
- **Generated code.** Models from a code generator are tested by the generator. Don''t re-test them downstream.

The cost of a useless test isn''t zero — it''s maintenance time every time the implementation changes. Every test you add is a brake on future refactoring. Add them deliberately.

### How tests guide design

When something is hard to test, that''s a *design signal*, not a testing problem. Look at the symptom:

- **Need to mock six things to test one method?** The method has too many dependencies. Split it.
- **The test needs `now()` to return a specific time?** Inject a `Clock` instead of calling `now()` directly. Same applies to `auth()->user()`, `request()`, `Cache::get()`.
- **The test reaches deep into private state?** The public API doesn''t expose what you actually need to verify. Add a query method.
- **Setup is 30 lines?** The unit under test has too much implicit context. Pull dependencies into the signature so the test can build them explicitly.

The line "this is hard to test" is a hint to refactor, not to write a more complex test.

### CI integration that actually matters

A test suite that isn''t run on every PR may as well not exist. The minimum bar:

1. **Run tests on every PR.** GitHub Actions, GitLab CI, Bitbucket Pipelines — whichever your team uses.
2. **Block merge on red.** Branch protection rules in GitHub. Not "ask people nicely".
3. **Fail fast.** Order tests roughly from cheapest to most expensive (unit → feature → integration). The first failure should appear inside two minutes.
4. **Cache dependencies.** `actions/cache` for `~/.composer` and `node_modules`. The difference between a 2-minute build and an 8-minute build is dev velocity.
5. **Surface flakes.** A test that passes 95% of the time will pass 95% of your PRs and fail 5% — exactly often enough to teach the team "just rerun the CI". That habit is fatal. Quarantine flakes (skip them while you fix them); don''t let them rot trust in the suite.

### Interview question: *"What''s your testing strategy?"*

A senior answer in three sentences:

1. **Feature tests** cover the happy path of every endpoint and the unhappy path of anything sensitive (auth, payments, permissions).
2. **Unit tests** for pure logic that''s hard to exercise through a feature test — calculations, parsers, anything stateless and gnarly.
3. **No tests** for getters/setters, configuration, framework-provided behaviour, or for "coverage" alone.

If the candidate names a coverage percentage as their strategy, they''re measuring the wrong thing. The strategy is "tests that would catch the bugs we''re actually shipping". Coverage is a side effect.
