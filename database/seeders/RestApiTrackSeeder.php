<?php

namespace Database\Seeders;

use App\Models\Challenge;
use App\Models\Path;
use App\Models\PathStep;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * "REST APIs & Auth" track (Option-A curriculum, track 2). Takes a dev with
 * Laravel basics and teaches them to design, build, validate, secure, and
 * diagnose a real API. Two Judge0 challenges (paginated resource shaping +
 * request validation), a guided auth lab, and an incident that bridges into
 * the observability moat (a 500 that should have been a 422).
 *
 * Challenge solutions/tests validated locally against the challenge runner's
 * supported assertions (assertSame / assertCount / assertGreaterThan).
 */
class RestApiTrackSeeder extends Seeder
{
    public function run(): void
    {
        $consultant = User::where('email', 'consultant@consultant.com')->firstOrFail();

        $this->seedChallenges($consultant->id);

        $path = Path::firstOrCreate(
            ['name' => 'REST APIs & Auth', 'consultant_id' => $consultant->id],
            ['description' => 'Design, build, validate, secure, and debug a real HTTP API in Laravel — the full-stack skill every Dublin job description assumes on day one.'],
        );

        foreach ($this->steps() as $step) {
            PathStep::updateOrCreate(
                ['path_id' => $path->id, 'order' => $step['order']],
                array_merge($step, ['path_id' => $path->id]),
            );
        }

        $this->command->info("Seeded 'REST APIs & Auth' with ".count($this->steps()).' steps + 2 challenges.');
    }

    private function seedChallenges(int $creatorId): void
    {
        Challenge::updateOrCreate(['slug' => 'api-paginated-resource'], [
            'title' => 'Shape a paginated JSON response',
            'difficulty' => 'intermediate',
            'is_premium' => false,
            'is_teaser' => false,
            'created_by' => $creatorId,
            'description' => 'Your `/jobs` endpoint returns a raw array — the front-end team needs a consistent, paginated envelope. Implement `paginate()` so every list endpoint answers with the same `{ data, meta }` shape.',
            'boilerplate_code' => <<<'PHP'
<?php

/**
 * Return a paginated JSON envelope for an API list endpoint:
 *
 *   [
 *     'data' => $items,
 *     'meta' => [
 *       'current_page' => int,
 *       'per_page'     => int,
 *       'total'        => int,
 *       'last_page'    => int,   // ceil(total / perPage), never less than 1
 *     ],
 *   ]
 *
 * @param array $items  the current page of records (already sliced)
 */
function paginate(array $items, int $page, int $perPage, int $total): array
{
    // TODO: build and return the envelope described above.
    return [];
}
PHP,
            'tests_code' => <<<'PHP'
<?php

class PaginatedResourceTest extends \PHPUnit\Framework\TestCase
{
    public function test_wraps_items_in_data()
    {
        $r = paginate([['id' => 1], ['id' => 2]], 1, 2, 5);
        $this->assertCount(2, $r['data']);
    }

    public function test_computes_meta()
    {
        $r = paginate([], 2, 10, 45);
        $this->assertSame(2, $r['meta']['current_page']);
        $this->assertSame(10, $r['meta']['per_page']);
        $this->assertSame(45, $r['meta']['total']);
        $this->assertSame(5, $r['meta']['last_page']); // ceil(45 / 10)
    }

    public function test_last_page_never_below_one()
    {
        $r = paginate([], 1, 10, 0);
        $this->assertSame(1, $r['meta']['last_page']);
    }
}
PHP,
        ]);

        Challenge::updateOrCreate(['slug' => 'api-validate-order'], [
            'title' => 'Validate a create-order request',
            'difficulty' => 'intermediate',
            'is_premium' => false,
            'is_teaser' => false,
            'created_by' => $creatorId,
            'description' => 'Bad input should never reach your business logic. Implement `validateOrder()` to reject a malformed create-order payload with a list of errors — the discipline that turns a 500 into a clean 422.',
            'boilerplate_code' => <<<'PHP'
<?php

/**
 * Validate a create-order payload. Return an array of error strings
 * (an empty array means the payload is valid). Rules:
 *
 *   product_id  required, integer, greater than 0
 *   quantity    required, integer, between 1 and 100 (inclusive)
 *   currency    required, one of: eur, brl
 */
function validateOrder(array $input): array
{
    $errors = [];
    // TODO: apply the three rules above, adding one message per failure.
    return $errors;
}
PHP,
            'tests_code' => <<<'PHP'
<?php

class ValidateOrderTest extends \PHPUnit\Framework\TestCase
{
    public function test_a_valid_payload_has_no_errors()
    {
        $this->assertCount(0, validateOrder(['product_id' => 5, 'quantity' => 2, 'currency' => 'eur']));
    }

    public function test_missing_product_id_is_rejected()
    {
        $this->assertGreaterThan(0, count(validateOrder(['quantity' => 2, 'currency' => 'eur'])));
    }

    public function test_quantity_out_of_range_is_rejected()
    {
        $this->assertGreaterThan(0, count(validateOrder(['product_id' => 5, 'quantity' => 0, 'currency' => 'eur'])));
        $this->assertGreaterThan(0, count(validateOrder(['product_id' => 5, 'quantity' => 101, 'currency' => 'eur'])));
    }

    public function test_unsupported_currency_is_rejected()
    {
        $this->assertGreaterThan(0, count(validateOrder(['product_id' => 5, 'quantity' => 2, 'currency' => 'usd'])));
    }
}
PHP,
        ]);
    }

    private function steps(): array
    {
        return [
            [
                'order' => 0,
                'type' => 'reading',
                'title' => 'What "RESTful" really means (and what interviewers check)',
                'difficulty' => 'beginner',
                'estimated_minutes' => 12,
                'tldr' => 'REST is resources (nouns) + HTTP verbs + honest status codes, with no server-side session between calls. Interviewers probe exactly those four things.',
                'concept_content' => <<<'MD'
## Core — the four ideas

An API is **RESTful** when it models your domain as **resources** (nouns like `orders`, `jobs`, `users`) that you act on with **HTTP verbs**:

- `GET /jobs` — list · `GET /jobs/42` — read one
- `POST /jobs` — create · `PUT/PATCH /jobs/42` — update · `DELETE /jobs/42` — remove

Two more rules matter as much as the URLs:

- **Honest status codes.** `200/201` success, `422` validation failed, `401` not authenticated, `403` authenticated-but-forbidden, `404` not found, `500` you broke.
- **Stateless.** Each request carries everything it needs (usually a token). The server keeps no per-client session between calls.

## Deeper dive — the parts that separate a real API from "some JSON over HTTP"

- **Consistency.** Every list looks the same (`{ data, meta }`), every error looks the same (`{ message, errors }`). Consistency is what lets a front-end team move fast against you.
- **Idempotency.** `GET`, `PUT`, and `DELETE` should be safe to retry; `POST` isn't. This matters the moment a network blips and a client retries.
- **Versioning.** `/api/v1/...` — so you can evolve without breaking existing clients.

## Senior insights — what an interviewer is really testing

When someone asks "is your API RESTful?", they're checking whether you'll **return the right status code under failure** and **keep contracts stable**. The junior answer describes verbs; the senior answer talks about error envelopes, idempotent retries, and not leaking a 500 when the real problem was bad input (you'll fix exactly that in this track's incident).
MD,
            ],
            [
                'order' => 1,
                'type' => 'reading',
                'title' => 'Design the resource: apiResource, controllers & JSON Resources',
                'difficulty' => 'intermediate',
                'estimated_minutes' => 12,
                'tldr' => 'Route::apiResource wires the 5 REST routes to a controller; a JsonResource controls exactly what shape leaves your API — never dump a raw model.',
                'concept_content' => <<<'MD'
## Core — from route to response in Laravel

One line gives you the whole REST surface:

```php
Route::apiResource('jobs', JobController::class);
```

That maps `index / store / show / update / destroy` to controller methods. Each method returns a **API Resource**, never the raw model:

```php
return JobResource::collection($jobs);   // list
return new JobResource($job);            // one
```

## Deeper dive — the Resource is your contract

A `JsonResource` decides exactly which fields — and which shape — leave your API:

```php
public function toArray($request): array
{
    return [
        'id'    => $this->id,
        'title' => $this->title,
        'company' => $this->when($this->relationLoaded('company'), fn () => $this->company->name),
    ];
}
```

Two habits that mark experience: use `$this->when(...)` so you never trigger an N+1 by touching an unloaded relation, and never expose internal columns (password hashes, soft-delete flags, foreign keys the client doesn't need).

## Senior insights — the envelope

Wrap lists in a consistent `{ data, meta }` shape so pagination is predictable — which is exactly the `paginate()` helper you'll build in the next step. A stable envelope is the difference between an API a team enjoys and one they curse.
MD,
            ],
            [
                'order' => 2,
                'type' => 'challenge',
                'title' => 'Challenge: shape a paginated JSON response',
                'difficulty' => 'intermediate',
                'estimated_minutes' => 15,
                'description' => 'Implement the consistent paginated envelope every list endpoint should return.',
                'challenge_slug' => 'api-paginated-resource',
            ],
            [
                'order' => 3,
                'type' => 'reading',
                'title' => 'Validation as your first line of defence',
                'difficulty' => 'intermediate',
                'estimated_minutes' => 10,
                'tldr' => 'Validate at the edge with a FormRequest; Laravel auto-returns 422 with field errors. Unvalidated input is how a bad request becomes a 500.',
                'concept_content' => <<<'MD'
## Core — reject bad input before it reaches your logic

Never trust the client. In Laravel, validate at the edge:

```php
$data = $request->validate([
    'product_id' => ['required', 'integer', 'min:1'],
    'quantity'   => ['required', 'integer', 'between:1,100'],
    'currency'   => ['required', 'in:eur,brl'],
]);
```

If it fails, Laravel automatically responds **422 Unprocessable Entity** with a `{ message, errors }` body — no work from you. Only *valid* data continues past this line.

## Deeper dive — FormRequests keep controllers clean

Move rules into a dedicated `FormRequest` so the controller only handles the happy path:

```php
public function store(StoreOrderRequest $request) { /* $request->validated() is safe */ }
```

`FormRequest` also holds `authorize()` — validation and authorization in one place, off the controller.

## Senior insights — the 422-not-500 rule

The single most common junior bug: using unvalidated input (`$input['quantity'] * $price`) when `quantity` is missing → a `TypeError` → a **500**. The client can't tell what they did wrong. Validate first and that same failure becomes a **422 with a clear message**. You'll diagnose this exact bug from telemetry later in this track.
MD,
            ],
            [
                'order' => 4,
                'type' => 'challenge',
                'title' => 'Challenge: validate a create-order request',
                'difficulty' => 'intermediate',
                'estimated_minutes' => 15,
                'description' => 'Implement the validation that turns a would-be 500 into a clean, field-level 422.',
                'challenge_slug' => 'api-validate-order',
            ],
            [
                'order' => 5,
                'type' => 'reading',
                'title' => 'Auth for APIs: tokens, Sanctum and the 401/403 line',
                'difficulty' => 'intermediate',
                'estimated_minutes' => 12,
                'tldr' => '401 = who are you? (no/invalid token). 403 = I know you, but no. Sanctum issues bearer tokens; middleware guards routes; abilities scope what a token can do.',
                'concept_content' => <<<'MD'
## Core — authentication vs authorization

Two different questions, two different status codes:

- **Authentication** — *who are you?* No or invalid token → **401 Unauthorized**.
- **Authorization** — *are you allowed to do this?* Valid token, wrong permission → **403 Forbidden**.

Mixing them up is a classic junior tell. "Logged in but not allowed" is **403**, never 401.

## Deeper dive — Sanctum in practice

Laravel Sanctum issues a **bearer token** on login; the client sends it as `Authorization: Bearer <token>`. Guard routes with middleware:

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('orders', OrderController::class);
});
```

Scope what a token can do with **abilities**: `$user->createToken('mobile', ['orders:read'])`, then check `$request->user()->tokenCan('orders:read')`.

## Senior insights — the threat model

Think about token **expiry and revocation** (a leaked token that never dies is a breach), and about **ownership**: authenticated isn't enough — user A must not read user B's orders. That ownership check (return 403 otherwise) is the lab you'll do next, and it's the pattern this very platform uses to stop one client seeing another's data.
MD,
            ],
            [
                'order' => 6,
                'type' => 'lab',
                'title' => 'Lab: secure an endpoint with auth + ownership',
                'difficulty' => 'intermediate',
                'estimated_minutes' => 25,
                'description' => 'Guided exercise: lock down an /orders API so only authenticated owners can read their own orders.',
                'instructions' => [
                    ['id' => 1, 'text' => 'Wrap the /orders routes in the auth:sanctum middleware group. Confirm an unauthenticated request now returns 401 (try it with curl and no token).'],
                    ['id' => 2, 'text' => 'In OrderController@show, load the order and compare its user_id to $request->user()->id. If they differ, throw an authorization error so the API returns 403 — not 404, not 200.'],
                    ['id' => 3, 'text' => 'Confirm the owner (valid token, own order) gets 200 with the JsonResource, and a different authenticated user gets 403.'],
                    ['id' => 4, 'text' => 'Bonus: scope the token with an ability (orders:read) and reject calls whose token lacks it with 403.'],
                ],
            ],
            [
                'order' => 7,
                'type' => 'incident',
                'title' => 'Incident: the API 500s when it should 422',
                'difficulty' => 'intermediate',
                'estimated_minutes' => 12,
                'description' => 'A create-order endpoint crashes on bad input. Read the telemetry and find why — and why it is the wrong status code.',
                'evidence' => [
                    'scenario' => 'Users report the "Place order" button sometimes shows a generic "Server error". Your monitoring shows a spike of 500s on POST /orders — but only for some requests. Here is a trace and the logs for one failing request.',
                    'trace' => [
                        'root' => 'POST /orders',
                        'spans' => [
                            ['id' => 'a', 'parent' => null, 'name' => 'POST /orders', 'service' => 'web', 'start' => 0, 'dur' => 210, 'kind' => 'server'],
                            ['id' => 'b', 'parent' => 'a', 'name' => 'AuthMiddleware', 'service' => 'web', 'start' => 2, 'dur' => 5, 'kind' => 'internal'],
                            ['id' => 'c', 'parent' => 'a', 'name' => 'OrderController@store', 'service' => 'web', 'start' => 8, 'dur' => 198, 'kind' => 'internal'],
                            ['id' => 'd', 'parent' => 'c', 'name' => 'computeTotal(qty × price)', 'service' => 'web', 'start' => 30, 'dur' => 2, 'kind' => 'internal'],
                        ],
                    ],
                    'logs' => [
                        ['t' => '14:22:07', 'level' => 'INFO', 'request_id' => 'req_o12', 'msg' => 'POST /orders payload={"product_id":5,"currency":"eur"}'],
                        ['t' => '14:22:07', 'level' => 'ERROR', 'request_id' => 'req_o12', 'msg' => 'TypeError: Unsupported operand types: int * null — OrderController::store():34'],
                        ['t' => '14:22:07', 'level' => 'INFO', 'request_id' => 'req_o12', 'msg' => 'response 500 Internal Server Error'],
                    ],
                ],
                'quiz' => [
                    ['id' => 1, 'question' => 'Looking at the logged payload, what did the client leave out?', 'options' => ['The auth token', 'The "quantity" field', 'The "product_id" field', 'Nothing — the payload is fine'], 'correct_index' => 1, 'explanation' => 'The payload is {product_id, currency} — no quantity. The code then does qty × price with qty = null.'],
                    ['id' => 2, 'question' => 'Why did the API return 500 instead of 422?', 'options' => ['The database was down', 'The controller used the input without validating it first, so a TypeError bubbled up', 'The route was misconfigured', 'Rate limiting kicked in'], 'correct_index' => 1, 'explanation' => 'No validation ran, so null reached computeTotal() and threw a TypeError — an unhandled 500. With validation, the missing field is a clean 422.'],
                    ['id' => 3, 'question' => 'What is the correct fix?', 'options' => ['Wrap store() in try/catch and return 500 more nicely', 'Validate the request (FormRequest) so a missing field returns 422 with a field error', 'Increase PHP memory_limit', 'Make the client retry automatically'], 'correct_index' => 1, 'explanation' => 'Validate at the edge. A missing/invalid field should be a 422 with { errors: { quantity: [...] } }, telling the client exactly what to fix — never a 500.'],
                ],
            ],
            [
                'order' => 8,
                'type' => 'quiz',
                'title' => 'Checkpoint: REST, validation & auth',
                'difficulty' => 'intermediate',
                'estimated_minutes' => 6,
                'description' => 'Lock in the status-code reflexes an interviewer will probe.',
                'quiz' => [
                    ['id' => 1, 'question' => 'A client requests a resource that does not exist. Correct status code?', 'options' => ['200', '301', '404', '500'], 'correct_index' => 2, 'explanation' => '404 Not Found — the request was fine, the thing just is not there.'],
                    ['id' => 2, 'question' => 'A POST fails validation (a required field is missing). Correct status code?', 'options' => ['200', '400', '422', '500'], 'correct_index' => 2, 'explanation' => '422 Unprocessable Entity — Laravel\'s default for failed validation, with a { message, errors } body.'],
                    ['id' => 3, 'question' => 'A valid, authenticated user tries an action they are not permitted to do. Status?', 'options' => ['401', '403', '404', '200'], 'correct_index' => 1, 'explanation' => '403 Forbidden — we know who you are; you just are not allowed.'],
                    ['id' => 4, 'question' => 'A request arrives with no token (or an invalid one). Status?', 'options' => ['400', '401', '403', '404'], 'correct_index' => 1, 'explanation' => '401 Unauthorized — authentication failed; who are you?'],
                ],
            ],
        ];
    }
}
