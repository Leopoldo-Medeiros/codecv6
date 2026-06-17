# Testing Guide (codecv6)

PHPUnit 11 testing patterns for the codecv6 Laravel 12 backend.

> NOTE: The frontend does NOT have a test runner configured. If Vitest is added later, expand this doc.

## Stack

- **PHPUnit 11**
- **`RefreshDatabase` trait** — fresh DB per test
- **Factories** — `UserFactory` auto-creates `Profile` and assigns `client` role via `afterCreating()`
- **Spatie Permission** — `RoleSeeder` must run in `setUp()` so roles exist
- **`Http::fake()`** for external services (Stripe, Gemini, Anthropic, Judge0, Jina AI)

## Commands

```bash
# All tests, compact output
ddev artisan test --compact

# Single file
ddev artisan test --compact tests/Feature/Api/CourseApiTest.php

# Filter by method name
ddev artisan test --compact --filter=test_admin_can_create_course

# Coverage (Xdebug is enabled in DDEV)
ddev artisan test --coverage

# Make a test
ddev artisan make:test Api/CourseApiTest

# Make a factory
ddev artisan make:factory CourseFactory --model=Course
```

## Test Boilerplate

```php
<?php

namespace Tests\Feature\Api;

use App\Models\Course;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class); // CRITICAL — Spatie roles must exist
    }

    public function test_authenticated_user_can_list_courses(): void
    {
        $user = User::factory()->create();
        Course::factory()->count(3)->create();

        $response = $this->actingAs($user)->getJson('/api/courses');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [['id', 'title']],
                'meta' => ['current_page', 'last_page', 'total'],
            ]);
    }
}
```

## Common Test Patterns

### Authorization Tests

```php
public function test_client_cannot_create_courses(): void
{
    $client = User::factory()->create(); // 'client' role by default

    $response = $this->actingAs($client)->postJson('/api/courses', [
        'title' => 'New Course',
    ]);

    $response->assertForbidden(); // 403 from `role:admin|consultant` middleware
}

public function test_consultant_can_create_courses(): void
{
    $consultant = User::factory()->create();
    $consultant->syncRoles(['consultant']);

    $response = $this->actingAs($consultant)->postJson('/api/courses', [
        'title' => 'New Course',
        'description' => 'Lorem ipsum',
    ]);

    $response->assertCreated()
        ->assertJsonFragment(['title' => 'New Course']);
}

public function test_unauthenticated_user_cannot_list_courses(): void
{
    $response = $this->getJson('/api/courses');
    $response->assertUnauthorized();
}
```

### Validation Tests

```php
public function test_course_title_is_required(): void
{
    $admin = $this->makeAdmin();

    $response = $this->actingAs($admin)->postJson('/api/courses', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['title']);
}

private function makeAdmin(): User
{
    return tap(User::factory()->create(), fn(User $u) => $u->syncRoles(['admin']));
}
```

### Service / Business-Rule Tests

```php
public function test_admin_cannot_delete_themselves(): void
{
    $admin = $this->makeAdmin();

    $response = $this->actingAs($admin)->deleteJson("/api/users/{$admin->id}");

    $response->assertForbidden();
}

public function test_cannot_delete_last_admin(): void
{
    $a = $this->makeAdmin();
    $b = $this->makeAdmin();

    // Delete one, leaving exactly one admin
    $this->actingAs($a)->deleteJson("/api/users/{$b->id}")->assertNoContent();

    // Now $a is the last admin — should be undeletable
    $response = $this->actingAs($a)->deleteJson("/api/users/{$a->id}");
    $response->assertForbidden();
}
```

### Pagination

```php
public function test_courses_index_paginates_at_20(): void
{
    $user = User::factory()->create();
    Course::factory()->count(25)->create();

    $response = $this->actingAs($user)->getJson('/api/courses');

    $response->assertOk()
        ->assertJsonPath('meta.per_page', 20)
        ->assertJsonPath('meta.total', 25)
        ->assertJsonCount(20, 'data');
}
```

### Pivot Sync

```php
public function test_consultant_can_assign_paths_to_plan(): void
{
    $consultant = $this->makeConsultant();
    $plan = Plan::factory()->create(['consultant_id' => $consultant->id]);
    $paths = Path::factory()->count(2)->create();

    $response = $this->actingAs($consultant)->postJson("/api/plans/{$plan->id}/paths", [
        'path_ids' => $paths->pluck('id')->all(),
    ]);

    $response->assertOk();
    $this->assertSame(2, $plan->paths()->count());
}
```

### Soft Deletes

```php
public function test_deleted_course_does_not_appear_in_index(): void
{
    $user = User::factory()->create();
    $course = Course::factory()->create();
    $course->delete(); // SoftDeletes

    $response = $this->actingAs($user)->getJson('/api/courses');

    $response->assertJsonMissing(['id' => $course->id]);
    $this->assertSoftDeleted($course);
}
```

## Mocking External APIs

### Stripe

```php
use Stripe\Webhook;

public function test_checkout_session_completed_marks_payment_paid(): void
{
    $user = User::factory()->create();
    $payment = Payment::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
        'stripe_session_id' => 'cs_test_123',
    ]);

    $payload = json_encode([
        'id' => 'evt_test',
        'type' => 'checkout.session.completed',
        'data' => ['object' => ['id' => 'cs_test_123']],
    ]);

    // Mock the signature check by overriding the static method via partial mock,
    // OR inject a signature using config('services.stripe.webhook_secret') and
    // Webhook::generateTestHeaderString().
    $secret = config('services.stripe.webhook_secret');
    $sigHeader = Webhook::generateTestHeaderString([
        'payload' => $payload,
        'secret' => $secret,
    ]);

    $response = $this->call(
        'POST',
        '/api/webhooks/stripe',
        [], [], [],
        ['HTTP_STRIPE_SIGNATURE' => $sigHeader, 'CONTENT_TYPE' => 'application/json'],
        $payload,
    );

    $response->assertOk();
    $this->assertSame('paid', $payment->fresh()->status);
}
```

### Gemini (CV Analysis)

```php
use Illuminate\Support\Facades\Http;

public function test_cv_analysis_returns_score(): void
{
    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [[
                'content' => ['parts' => [[
                    'text' => json_encode([
                        'score' => 85,
                        'summary' => 'Strong match',
                        'matched_keywords' => ['Laravel', 'Vue'],
                        'missing_keywords' => ['Docker'],
                        'strengths' => ['Backend depth'],
                        'improvements' => ['Add cloud experience'],
                    ]),
                ]]],
            ]],
        ]),
    ]);

    $user = User::factory()->create();
    $response = $this->actingAs($user)->postJson('/api/cv/analyse', [
        'pdf' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
        'job_description' => 'Looking for a Laravel + Vue dev',
    ]);

    $response->assertOk()
        ->assertJsonPath('score', 85);
}
```

### Anthropic (LinkedIn Analyser)

```php
Http::fake([
    'api.anthropic.com/*' => Http::response([
        'content' => [['type' => 'text', 'text' => '{"score":78,"feedback":"..."}']],
    ]),
]);
```

### Judge0 (Challenges)

```php
Http::fake([
    'judge0-ce.p.rapidapi.com/*' => Http::response([
        'status' => ['id' => 3], // Accepted
        'stdout' => base64_encode(json_encode([
            'passed' => true,
            'tests' => [['name' => 'addsTwoNumbers', 'passed' => true]],
        ])),
        'stderr' => null,
    ]),
]);
```

### Jina AI (URL → text)

```php
Http::fake([
    'r.jina.ai/*' => Http::response('Page text content here.'),
]);
```

## Factory Helpers

```php
// tests/TestCase.php  — shared helpers
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function makeAdmin(): User
    {
        return tap(User::factory()->create(), fn(User $u) => $u->syncRoles(['admin']));
    }

    protected function makeConsultant(): User
    {
        return tap(User::factory()->create(), fn(User $u) => $u->syncRoles(['consultant']));
    }

    protected function makeClient(): User
    {
        return User::factory()->create(); // already 'client' via UserFactory
    }
}
```

## Test Organisation

- `tests/Feature/Api/*ApiTest.php` — HTTP endpoint tests
- `tests/Feature/Auth/*Test.php` — login, register, OAuth, password reset
- `tests/Unit/Services/*Test.php` — service-layer unit tests (no HTTP)
- `tests/Unit/Models/*Test.php` — model methods, scopes, relationships

## Best Practices

- **Always seed `RoleSeeder` in `setUp()`** — Spatie roles must exist
- **Use factories**, not manual `User::create(...)` calls
- **Test happy path + at least one failure** (401, 403, 422)
- **Mock external HTTP** with `Http::fake()` — never hit real APIs in tests
- **Keep tests deterministic** — `Carbon::setTestNow()` for time-dependent code
- **Run the full suite** before pushing: `ddev artisan test --compact`

## TDD for Bug Fixes

1. Reproduce the bug — write a failing test that captures it
2. Run: `ddev artisan test --compact --filter=test_the_bug`
3. See it fail
4. Fix the code
5. Run again — see it pass
6. Run the full suite — make sure nothing else broke

## Coverage

```bash
ddev artisan test --coverage --min=70
```

Coverage targets:
- Critical business logic (services dealing with payments, auth, role changes): **near 100%**
- API endpoints: cover all expected status codes (200, 201, 204, 401, 403, 404, 422)
- Models: methods and scopes
- Don't chase percentages on trivial code

## Common Gotchas

- **`syncRoles(['admin'])` throws "role does not exist"** → `$this->seed(RoleSeeder::class)` missing
- **`actingAs($user)` doesn't authenticate** → guard mismatch; use `actingAs($user, 'sanctum')` if needed
- **Soft-deleted rows leak into queries** → use `withTrashed()` deliberately or assert with `assertSoftDeleted()`
- **External HTTP not mocked** → tests slow and flaky; always `Http::fake()`
- **`config()` cache stale in tests** → tests should not depend on `.env`; use `phpunit.xml` env overrides
