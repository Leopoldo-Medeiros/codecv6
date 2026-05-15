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
                        'tldr' => 'PHP 8 turned a famously loose language into one with a real type system. Strict types catch a huge class of bugs at the call site; union, nullable and intersection types describe intent; enums replace the magic-string constants that have haunted PHP codebases for two decades.',
                        'estimated_minutes' => 18,
                        'difficulty' => 'intermediate',
                        'prerequisites' => [
                            ['id' => 1, 'title' => 'PHP installation & first script'],
                            ['id' => 2, 'title' => 'Variables, control flow and functions'],
                        ],
                        'concepts' => ['strict_types', 'union-types', 'nullable', 'intersection-types', 'enums', 'type-juggling'],
                        'has_playground' => true,
                        'playground_starter_code' => <<<'EOT'
<?php
declare(strict_types=1);

enum OrderStatus: string {
    case Pending  = 'pending';
    case Paid     = 'paid';
    case Refunded = 'refunded';
}

function describe(OrderStatus $status): string {
    return match ($status) {
        OrderStatus::Pending  => 'Awaiting payment',
        OrderStatus::Paid     => 'Paid in full',
        OrderStatus::Refunded => 'Refunded to customer',
    };
}

echo describe(OrderStatus::Paid);
EOT,
                        'concept_content' => <<<'EOT'
## Core (PHP fundamentals)

### strict_types: opting out of type juggling

Add this single line at the top of every PHP file you write:

```php
declare(strict_types=1);
```

It turns off automatic type coercion for the *current file*'s function calls. Without it, PHP silently converts `'42'` to `42` when you call a function expecting `int`. With it, you get a `TypeError` at the boundary — exactly where the bug is, not three layers deep where the wrong type finally explodes.

```php
declare(strict_types=1);

function tax(int $cents): int {
    return (int) round($cents * 0.23);
}

tax(150);     // 35
tax('150');   // TypeError — strict types blocks the implicit cast
```

This is the single highest-impact line you can add to a PHP project that didn't have it.

### Parameter, return and property types

PHP supports type declarations on **parameters**, **return values**, and (since 7.4) **class properties**:

```php
class Invoice {
    public int $number;
    public float $total = 0.0;

    public function addItem(string $sku, int $qty, float $price): void {
        $this->total += $qty * $price;
    }

    public function format(): string {
        return sprintf('#%d — €%.2f', $this->number, $this->total);
    }
}
```

The `void` return type means "this function returns nothing useful". Use it explicitly to make intent unambiguous to both readers and the IDE.

### Nullable shorthand: ?string

Real data has gaps. A user might not have a `linkedin_url`. The `?` prefix means "this OR null":

```php
function profileUrl(?string $handle): ?string {
    if ($handle === null) return null;
    return "https://linkedin.com/in/{$handle}";
}
```

`?string` is sugar for `string|null`. Both work; `?` reads cleaner for the common nullable case.

### Union types: one of several

PHP 8 added the `|` syntax for parameters that legitimately accept multiple types:

```php
function formatId(int|string $id): string {
    return is_int($id) ? "INT-{$id}" : "STR-{$id}";
}

formatId(42);       // "INT-42"
formatId('abc');    // "STR-abc"
```

Use unions sparingly. If you find yourself writing `int|string|float|bool`, that's a smell — your function is doing too much.

## Deeper dive (intermediate territory)

### Intersection types: combining contracts

Where unions say "one of these", intersections say "all of these at once". Useful when a parameter must implement multiple interfaces simultaneously:

```php
function dumpSize(Countable&Stringable $value): string {
    return count($value) . ': ' . $value;
}
```

The parameter must implement **both** `Countable` and `Stringable`. The compiler enforces it; no runtime checks needed. Intersections compose well with the standard SPL interfaces (`Iterator`, `Countable`, `Stringable`, `JsonSerializable`) and your own contracts.

### Enums: the death of magic strings

Before PHP 8.1, status fields were stored as untyped strings or class constants. Both invited typos:

```php
// ❌ Pre-enum: a typo silently becomes a wrong state
if ($order->status === 'refunded') { /* ... */ }
```

Enums replace this with a *closed set* of values the type system knows about:

```php
enum OrderStatus: string {
    case Pending  = 'pending';
    case Paid     = 'paid';
    case Refunded = 'refunded';

    public function isFinal(): bool {
        return match ($this) {
            self::Paid, self::Refunded => true,
            self::Pending              => false,
        };
    }
}

function notify(OrderStatus $status): void {
    if ($status === OrderStatus::Refunded) { /* ... */ }
}
```

Three real wins:
- **No typos** — `OrderStatus::Refundd` won't compile.
- **Exhaustive `match`** — add a new case and the compiler points at every `match` that doesn't handle it.
- **Methods on the enum** — `isFinal()` lives next to the data, not scattered across helpers.

### Backed enums for persistence

Backed enums (`: string` or `: int`) serialise to the database and back. Use `OrderStatus::from('paid')` to hydrate, `OrderStatus::tryFrom($value)` when the input might be invalid:

```php
$status = OrderStatus::tryFrom($row['status']) ?? OrderStatus::Pending;
```

String-backed for human-readable storage. Int-backed when you need compact indexed columns (e.g. a high-write events table). Laravel's `Eloquent` casts (`'status' => OrderStatus::class`) make the model property a real enum without lifting a finger.

### Property covariance and variance gotchas

PHP allows return-type **covariance** (a child can narrow the return) but parameter types are **invariant** in most cases. This bites when refactoring inheritance:

```php
class Repository {
    public function find(int $id): ?Model { /* ... */ }
}

class UserRepository extends Repository {
    public function find(int $id): ?User { /* OK — covariant return */ }
}
```

But you cannot widen a parameter in a child — that breaks LSP and PHP rightly refuses. When you hit one of these errors, the fix is almost always to redesign the contract rather than fight the type system.

## Senior insights (architecture & interview prep)

### Code-review red flags

Things to call out the next time you review a teammate's PR:

- **`mixed` everywhere.** A function returning `mixed` is a function that gave up on types. Push for a union or a value-object wrapper.
- **`@param string $foo` PHPDoc instead of a real type.** PHPDoc is hints; only the declaration is enforced. If the parameter can be typed, type it.
- **`stdClass` instead of a DTO.** "We'll just decode JSON to stdClass for now" is how every PHP codebase ends up with untyped property access bugs in production. Decode into a typed class.
- **`null` returned from a method whose name promises a value.** `getUser(): ?User` is sometimes right, but `currentUser(): ?User` smells — call sites end up with `if ($user === null)` everywhere. A `requireUser(): User` that throws is often the right contract.
- **A method that accepts `string|int|array`.** That's a function doing three jobs. Split it.

### When to adopt strict_types in a legacy codebase

Strict types is per-file. You don't have to migrate everything at once — that's a feature, not a workaround. A pragmatic rollout:

1. **Add it to new files only.** Set a lint rule (`php-cs-fixer` `declare_strict_types`) so every new PHP file ships with `declare(strict_types=1)` at the top.
2. **Migrate file-by-file when you touch a file for another reason.** Boy-scout rule: leave it cleaner than you found it. Don't open dedicated "add strict types to module X" PRs — they're large, mechanical, and high-blast-radius.
3. **Run the test suite after each batch.** Strict types surfaces real bugs (typically: `$_GET`/`$_POST` strings being passed unchanged to int-typed functions). The test suite is your safety net.

If a function genuinely needs to accept loose input (e.g. an HTTP request handler before validation), normalise at the boundary — don't disable strict types for the entire file.

### Trade-offs to discuss with your team

- **Enums vs. database-friendly constants.** Enums break older Laravel ecosystems that didn't expect them; some packages serialise them inconsistently. Keep an `->value` accessor handy.
- **Union types vs. polymorphism.** A union of two types in one function is often a missed opportunity for two methods or a small interface. Use unions when the two types have legitimately the same processing path.
- **Performance.** Type declarations cost nothing at runtime — opcache caches the parsed AST. Don't let anyone tell you otherwise.

### What interviewers listen for

Common technical-screen prompts and the bullet they're waiting for:

- *"How do PHP comparisons work?"* — Mention strict (`===`) vs loose (`==`), then go straight to the security angle: type-juggling in `==` is exploitable when comparing user input against tokens or roles.
- *"What's the difference between an abstract class and an interface?"* — Senior answer: interfaces describe a contract; abstract classes share *implementation*. PHP 8 added interface intersection types — interfaces have grown more expressive, abstract classes less necessary.
- *"How would you refactor a function that returns `mixed`?"* — Start by enumerating the actual return shapes the function produces. Replace with a union, a value object, or a sealed hierarchy of result types. Show that you reach for types as a *design* tool, not a syntax tax.

The senior bar isn't memorising every union/intersection rule. It's reaching for the type system as a way to make wrong code unrepresentable.
EOT,
                        'resources' => [
                            ['label' => 'PHP 8.3 Type System', 'url' => 'https://www.php.net/manual/en/language.types.declarations.php'],
                            ['label' => 'PHP Enums — official docs', 'url' => 'https://www.php.net/manual/en/language.enumerations.php'],
                            ['label' => 'Typed Properties RFC', 'url' => 'https://wiki.php.net/rfc/typed_properties_v2'],
                        ],
                        'instructions' => [
                            [
                                'id' => 1,
                                'text' => "Form payload sanitizer — write sanitizeSignup(array \$raw): array that takes \$_POST-style input and returns typed fields: email (trimmed string), age (int, 0 if missing), terms_accepted (bool from 'yes'/'1'/'true'), referral_code (?string). This is what every onboarding controller does on day one.",
                                'starter_code' => <<<'PHP'
<?php
declare(strict_types=1);

/**
 * Sanitize a raw $_POST-style array into typed fields.
 *   - email          → trimmed string
 *   - age            → int (0 if missing)
 *   - terms_accepted → bool, true when value is 'yes' / '1' / 'true' (case-insensitive)
 *   - referral_code  → string or null
 */
function sanitizeSignup(array $raw): array
{
    // TODO: implement
    return [];
}

// Try your implementation against these:
var_dump(sanitizeSignup([
    'email' => '  alice@example.com  ',
    'age' => '25',
    'terms_accepted' => 'yes',
    'referral_code' => 'WELCOME10',
]));
var_dump(sanitizeSignup([]));
var_dump(sanitizeSignup(['terms_accepted' => 'no']));
PHP,
                            ],
                            [
                                'id' => 2,
                                'text' => "Strict types refactor — a teammate wrote function calculateDiscount(\$price, \$percent) { return \$price - (\$price * \$percent / 100); }. Add declare(strict_types=1), parameter types, and a return type. Then call it with ('10.50', 10) and explain what changes between strict and non-strict mode.",
                                'starter_code' => <<<'PHP'
<?php
// Step 1: add declare(strict_types=1); on the line above this comment.

// Step 2: add parameter types and a return type to this function.
function calculateDiscount($price, $percent)
{
    return $price - ($price * $percent / 100);
}

// Step 3: run BOTH calls and see what changes between strict and non-strict mode.
echo calculateDiscount(10.50, 10) . PHP_EOL;     // 9.45
echo calculateDiscount('10.50', 10) . PHP_EOL;   // ← with strict_types this throws TypeError
PHP,
                            ],
                            [
                                'id' => 3,
                                'text' => "Security: type juggling bypass — explain why if (\$_GET['role'] == 'admin') is exploitable when a request is crafted as ?role=0 in PHP 7. Fix it with strict equality and a whitelist. Classic technical-screen question for senior backend roles.",
                                'starter_code' => <<<'PHP'
<?php
// The vulnerable controller below is in production. Two things to do:
//
//  1. In a comment, explain WHY this returns true when an attacker sends
//     ?role=0 under PHP 7 semantics. (Hint: 'admin' == 0)
//
//  2. Rewrite isAdmin() using strict equality and a whitelist of allowed
//     roles. Make it impossible to bypass with crafted input.

function isAdmin(array $request): bool
{
    return $request['role'] == 'admin'; // ← exploitable
}

// Crafted requests — your fixed version must return false for both.
var_dump(isAdmin(['role' => 0]));        // current code: true (BUG)
var_dump(isAdmin(['role' => 'admin']));  // legitimate admin: true
var_dump(isAdmin(['role' => 'user']));   // regular user: false
PHP,
                            ],
                            [
                                'id' => 4,
                                'text' => "Convert magic strings to an enum — find any model in your project that has a status column stored as a raw string. Define a backed enum, cast the property, and update one match() that checks the status to be exhaustive.",
                                'starter_code' => <<<'PHP'
<?php
declare(strict_types=1);

// Define a string-backed enum OrderStatus with three cases:
//   Pending = 'pending', Paid = 'paid', Refunded = 'refunded'
//
// Then add a method isFinal(): bool that uses an exhaustive match()
// — Paid and Refunded are final, Pending is not.

enum OrderStatus: string
{
    // TODO: cases
}

// Once defined, this should compile and run.
function describe(OrderStatus $status): string
{
    return match ($status) {
        // TODO: cover every case exhaustively — drop any case here and
        // your IDE/PHPStan should immediately flag it as unhandled.
    };
}

// echo describe(OrderStatus::Paid);
// var_dump(OrderStatus::Pending->isFinal());   // false
// var_dump(OrderStatus::Refunded->isFinal());  // true
PHP,
                            ],
                            [
                                'id' => 5,
                                'text' => "Money math — a junior writes \$total = 0.1 + 0.2 and asserts === 0.3. Explain why the assertion fails. Then implement sumCents(array \$stringPrices): int that takes ['9.99', '1.50', '0.01'] and returns the total in cents (integer) — the only safe way to handle money in any language.",
                                'starter_code' => <<<'PHP'
<?php
declare(strict_types=1);

// Part 1 — convince yourself the float assertion fails.
var_dump(0.1 + 0.2);                  // 0.30000000000000004
var_dump(0.1 + 0.2 === 0.3);          // false  ← IEEE 754 talking

// Part 2 — implement sumCents.
// Take prices as strings (the way HTTP forms and CSV files give them),
// convert each to integer cents (e.g. '9.99' → 999), and sum.
function sumCents(array $stringPrices): int
{
    // TODO: implement — avoid floats entirely if you can.
    return 0;
}

echo sumCents(['9.99', '1.50', '0.01']) . PHP_EOL;   // expected: 1150
echo sumCents(['100.00', '0.99']) . PHP_EOL;         // expected: 10099
echo sumCents([]) . PHP_EOL;                          // expected: 0
PHP,
                            ],
                        ],
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Laravel Request Lifecycle: What Happens Before Your Code Runs',
                        'type' => 'reading',
                        'description' => 'Most juniors treat Laravel as magic. Understanding the lifecycle — from index.php to the Response — is what separates problem-solvers from tutorial-followers. We dissect bootstrap, Service Providers, the IoC Container, and how middleware forms a pipeline.',
                        'tldr' => 'Every HTTP request walks the same eight-stage road: index.php → Application → Service Providers → HTTP Kernel → global middleware → router → route middleware → your controller. Knowing where you are on that road turns "Laravel is magic" debugging into "I can put a breakpoint anywhere".',
                        'estimated_minutes' => 22,
                        'difficulty' => 'intermediate',
                        'prerequisites' => [
                            ['id' => 1, 'title' => 'Modern PHP: Types, Nullables and Enums'],
                            ['id' => 2, 'title' => 'Basic Laravel routing & controllers'],
                        ],
                        'concepts' => ['lifecycle', 'service-providers', 'ioc-container', 'middleware', 'http-kernel', 'service-resolution'],
                        'has_playground' => true,
                        'playground_starter_code' => <<<'PHP'
<?php
declare(strict_types=1);

// A toy middleware that logs each request's path + duration.
// Copy this into app/Http/Middleware/ in a real Laravel app and
// register it in bootstrap/app.php to see it in your laravel.log.

class LogRequestDuration
{
    public function handle(\Illuminate\Http\Request $request, \Closure $next)
    {
        $startedAt = microtime(true);

        // ── Pre-handle phase: runs BEFORE the controller. Authentication,
        //    rate limiting, header massaging all happen here.
        \Log::info('→ request', ['path' => $request->path()]);

        $response = $next($request);  // ← controller (and everything after) runs here

        // ── Post-handle phase: runs AFTER the response is built.
        //    Logging, header injection, span finalisation belong here.
        $durationMs = (int) ((microtime(true) - $startedAt) * 1000);
        \Log::info('← response', [
            'path' => $request->path(),
            'status' => $response->getStatusCode(),
            'duration_ms' => $durationMs,
        ]);

        return $response;
    }
}

echo "Middleware skeleton compiled OK\n";
PHP,
                        'concept_content' => <<<'EOT'
## Core (PHP fundamentals)

### The journey of a request

Every request to a Laravel app walks the same road. Burn this picture into your head — it tells you where to put a breakpoint when something goes wrong.

```mermaid
flowchart TD
    REQ(["HTTP Request"]):::emerald
    REQ --> ENTRY["public/index.php\nApp entry point"]:::slate
    ENTRY --> BOOTSTRAP["bootstrap/app.php\ncreates Application"]:::slate
    BOOTSTRAP --> PROVIDERS["Service Providers\nregister() → boot()"]:::blue
    PROVIDERS --> KERNEL["HTTP Kernel\nhandle()"]:::slate
    KERNEL --> GMID["Global Middleware\nTrimStrings, VerifyCsrf, …"]:::slate
    GMID --> ROUTER["Router\nmatches URI + HTTP verb"]:::emerald
    ROUTER --> RMID["Route Middleware\nauth, throttle, role, …"]:::slate
    RMID --> CTRL["Controller Action\n← your code runs here"]:::emerald
    CTRL --> RES["Eloquent Resource\nshapes the response"]:::emerald
    RES --> RESP(["HTTP Response"]):::emerald
    classDef emerald fill:#d1fae5,stroke:#059669,color:#065f46,font-weight:600
    classDef slate   fill:#f1f5f9,stroke:#64748b,color:#1e293b,font-weight:500
    classDef blue    fill:#dbeafe,stroke:#3b82f6,color:#1e40af,font-weight:500
```

If you're debugging a 500 and don't know which arrow to inspect first, you're guessing. After this step you should be able to point at one box and say *"the bug is between here and here"*.

### The entry point

Every web request first hits `public/index.php`. It does four things in order:

```php
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
```

1. **Autoload** PSR-4 classes through Composer.
2. **Build** the `Application` instance (the IoC container).
3. **Capture** the incoming HTTP request into an `Illuminate\Http\Request`.
4. **Run** it through the HTTP Kernel.

Everything else is sugar on top of these four lines.

### Service Providers — register vs boot

A Service Provider is the bridge between *features Laravel ships with* and *your application's bindings*. Each provider runs two phases:

```php
class AppServiceProvider extends ServiceProvider
{
    // register(): bind things into the container.
    // The container isn't fully populated yet — DO NOT use other services here.
    public function register(): void
    {
        $this->app->singleton(BillingClient::class, fn () => new StripeBillingClient(
            config('services.stripe.secret')
        ));
    }

    // boot(): everything is registered. Now you can safely USE other services.
    public function boot(): void
    {
        Gate::define('manage-billing', fn ($user) => $user->hasRole('admin'));
    }
}
```

If you put `Gate::define` inside `register()`, the Auth service might not exist yet and your app crashes at boot time. The two-phase split is the framework's way of avoiding circular dependencies.

### Middleware pipeline

Middleware sits *between* the request reaching the router and your controller running. Each middleware can:

- **Reject** the request early (return a response without calling `$next`).
- **Mutate** the request before the controller sees it.
- **Mutate** the response before the client sees it.

```php
public function handle(Request $request, Closure $next)
{
    // pre-handle phase
    if (! $request->bearerToken()) {
        return response()->json(['error' => 'unauthenticated'], 401);
    }

    $response = $next($request);

    // post-handle phase
    $response->headers->set('X-Trace-Id', request()->header('X-Trace-Id', uniqid()));
    return $response;
}
```

The `$next($request)` call is the seam: everything before it runs on the way **in**, everything after runs on the way **out**. Authentication, rate limiting, and request rewriting belong before; logging, header injection and tracing belong after.

## Deeper dive (container internals)

### Bindings: `bind`, `singleton`, `instance`, `scoped`

The container has four primary registration modes. Pick wrong and you either leak state or pay for object construction on every request:

| Method                       | New instance per resolve? | Lives for…          | Use when…                                                |
| ---------------------------- | ------------------------- | ------------------- | -------------------------------------------------------- |
| `bind`                       | Yes                       | One resolve         | The dependency holds per-request state (request stamper, transient builder). |
| `singleton`                  | No                        | The whole process   | The dependency is stateless or expensive to build (HTTP clients, DB connections via the factory). |
| `instance`                   | No                        | The whole process   | You already have the object and just want to register it. |
| `scoped`                     | No                        | One request/job     | Per-request caching (the request id, an idempotency key store). Octane-safe. |

Choosing `singleton` for a stateful service is the classic Octane footgun: state from one request leaks into the next. When you migrate a Laravel app to Octane, every `singleton` becomes suspect.

### Contextual binding

Same interface, different implementation depending on who's asking:

```php
$this->app
    ->when(EmailNotifier::class)
    ->needs(MailerInterface::class)
    ->give(ResendMailer::class);

$this->app
    ->when(InvoiceJob::class)
    ->needs(MailerInterface::class)
    ->give(SmtpMailer::class);
```

`EmailNotifier` gets the Resend client (fast, transactional). `InvoiceJob` gets the SMTP client (slower but uses the audit-logged corporate relay). The consumer doesn't change; the wiring does.

### Route caching and what it actually caches

`php artisan route:cache` serialises the matched route definitions to a single file. It doesn't cache responses. The big gotcha: **closure-based routes can't be cached** — they aren't serialisable. If your bootstrap suddenly fails in production, that's almost always why. Convert closures to controller actions before caching.

### The deferred-loading optimisation

Service Providers run on every request by default. If you only need a provider when a specific job runs, mark it `deferred`:

```php
class HeavyAnalyticsProvider extends ServiceProvider implements DeferrableProvider
{
    public function provides(): array
    {
        return [AnalyticsClient::class];
    }
}
```

The provider's `register()` won't fire until something type-hints `AnalyticsClient`. For background jobs that don't touch analytics, you skip the bootstrap cost entirely.

## Senior insights (debugging & interview prep)

### Reading a Laravel stack trace efficiently

When prod 500s, you have a 60-line stack trace. The trick: read from the **bottom up** until you cross the framework/app boundary. Every line under `vendor/laravel/framework/...` is the framework calling your code; the first line in `app/` is *your* bug.

Practical tools:

- `dd(debug_backtrace())` shows the same trace inline if you want to inspect a path without raising an exception.
- `\Log::stack(['daily'])->channel('errors')->debug(...)` for production-safe instrumentation.
- The `Throwable::getPrevious()` chain matters — the root cause is usually 2-3 wrappers deep (request → controller → service → repository → DB exception).

### Common interview question: *"Walk me through what happens when you submit a form."*

A senior answer hits these beats in order — short, no umm-ing:

1. The browser POSTs to a URL. The web server (nginx/Apache) forwards to PHP-FPM which calls `public/index.php`.
2. `bootstrap/app.php` builds the Application container.
3. Service Providers register (and then boot) — anything your app depends on becomes resolvable.
4. The HTTP Kernel runs global middleware: `TrimStrings`, `VerifyCsrfToken`, `EncryptCookies`, etc.
5. The router matches the URI to a route definition.
6. Route middleware runs: `auth`, `throttle`, custom role checks.
7. Your controller resolves its dependencies through the container (constructor and method injection) and runs.
8. The controller returns something the framework wraps in `Symfony\Component\HttpFoundation\Response`.
9. The response walks back through every middleware that ran on the way in — this time their post-handle phase fires.
10. PHP-FPM emits the response; the web server returns it to the browser.

If you can recite that flow and pin a known framework feature (Sanctum's `EnsureFrontendRequestsAreStateful`, the new bootstrap/app.php fluent API) to each step, you're already past the senior bar most interviewers set.

### Anti-patterns seniors flag at code review

- **Container access inside loops.** `app(Foo::class)` resolves once per iteration. Inject once at the top.
- **`request()` and `auth()` helpers deep inside service classes.** Couples the service to the HTTP cycle and makes it untestable from a queue worker. Inject what you need explicitly.
- **Global middleware doing per-route work.** Authentication for an entire app belongs in global middleware. Authentication for `/admin/*` belongs on the route group. Mixing them is how you accidentally protect the wrong routes after a refactor.
- **`Schema::create` in a service provider.** Service providers run on boot; migrations belong in migrations. Mixing them creates "works locally, doesn't work in production".

### Custom middleware design tips

When you write your own middleware:

- Make the constructor injection explicit — no `app()` lookups.
- Return early on rejection. Don't carry state past `$next($request)` if you don't need to.
- If you mutate the request, add a clear `data-injected-by` header or a span attribute so the next person can find where the mutation happened.

Middleware is the cleanest place to attach cross-cutting telemetry (trace ids, request logs). Resist the temptation to put business logic there — it runs on every request, including ones that have nothing to do with the feature.
EOT,
                        'resources' => [
                            ['label' => 'Laravel Request Lifecycle', 'url' => 'https://laravel.com/docs/lifecycle'],
                            ['label' => 'Service Container', 'url' => 'https://laravel.com/docs/container'],
                            ['label' => 'Service Providers', 'url' => 'https://laravel.com/docs/providers'],
                            ['label' => 'Middleware', 'url' => 'https://laravel.com/docs/middleware'],
                        ],
                        'instructions' => [
                            [
                                'id' => 1,
                                'text' => "Trace a real request — in a fresh Laravel app, add a route that returns 'hi'. Put dd(microtime(true)) calls in: bootstrap/app.php, your AppServiceProvider's boot(), the route definition. Compare the timestamps to see exactly how much time is spent in each phase.",
                                'starter_code' => null,
                            ],
                            [
                                'id' => 2,
                                'text' => "Write a request-duration middleware — implement RequestTimer that logs request method/path/status/duration_ms via Log::info. Register it as global middleware. Confirm it fires for every route by hitting two endpoints and reading storage/logs/laravel.log.",
                                'starter_code' => <<<'PHP'
<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequestTimer
{
    public function handle(Request $request, Closure $next)
    {
        // TODO: capture microtime(true) before calling $next.
        // TODO: call $next($request) to get the response.
        // TODO: log method, path, response status, duration_ms.
        // TODO: return the response.
    }
}
PHP,
                            ],
                            [
                                'id' => 3,
                                'text' => "Spot the register vs boot bug — given the snippet below, explain why putting Gate::define in register() can crash on certain Laravel versions, then fix it by moving the call to boot().",
                                'starter_code' => <<<'PHP'
<?php
declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // BUG: Auth-related services may not exist yet when register() runs.
        // Gate facade resolves the AuthManager from the container, which
        // depends on the session, which depends on the cookie service, etc.
        Gate::define('manage-billing', fn ($user) => $user->hasRole('admin'));
    }

    public function boot(): void
    {
        // TODO: move the Gate::define call here.
    }
}
PHP,
                            ],
                            [
                                'id' => 4,
                                'text' => "Container singleton vs bind — given an InvoiceClock that returns now(), explain what changes if you register it as singleton vs bind. Then think about Octane: which one is the footgun and why? Senior interview territory.",
                                'starter_code' => <<<'PHP'
<?php
declare(strict_types=1);

// In a real provider you would do either:
// $this->app->bind(InvoiceClock::class, fn () => new InvoiceClock());
// $this->app->singleton(InvoiceClock::class, fn () => new InvoiceClock());

class InvoiceClock
{
    public readonly \DateTimeImmutable $bornAt;

    public function __construct()
    {
        $this->bornAt = new \DateTimeImmutable();
    }

    public function now(): \DateTimeImmutable
    {
        return $this->bornAt;  // ← deliberately stale; mirrors a real bug we want to expose
    }
}

// Simulate two requests by resolving the clock twice with a small sleep.
$clockA = new InvoiceClock();
sleep(1);
$clockB = new InvoiceClock();   // ← in singleton mode you'd get the SAME instance here

echo $clockA->now()->format('H:i:s.u') . PHP_EOL;
echo $clockB->now()->format('H:i:s.u') . PHP_EOL;
PHP,
                            ],
                            [
                                'id' => 5,
                                'text' => "Walk-through interview drill — write a 10-step walk-through of what happens between submitting a login form (POST /login) and the user seeing the dashboard. One sentence per step, no skipping middleware or the response phase. Read it back; if it sounds memorised rather than reasoned, that's exactly what an interviewer will catch.",
                                'starter_code' => null,
                            ],
                        ],
                        'challenge_prompt' => null,
                        'lab_url' => null,
                    ],
                    [
                        'title' => 'Advanced Eloquent: Scopes, Accessors and Events',
                        'type' => 'reading',
                        'description' => 'Eloquent is powerful but easily misused. We cover query scopes for business rules, accessors/casts for shape transformation, model events and observers, and — most importantly — when to stop using Eloquent and write raw SQL.',
                        'tldr' => 'Eloquent gives you four levers — scopes (reusable WHERE clauses), accessors/casts (column ↔ PHP type), observers (lifecycle hooks), and `whereRaw` (the escape hatch). Knowing which lever to pull for which problem is what separates a senior Laravel dev from someone who only writes `Model::all()->filter(...)`.',
                        'estimated_minutes' => 25,
                        'difficulty' => 'intermediate',
                        'prerequisites' => [
                            ['id' => 1, 'title' => 'Modern PHP: Types, Nullables and Enums'],
                            ['id' => 2, 'title' => 'Laravel Request Lifecycle'],
                        ],
                        'concepts' => ['query-scopes', 'accessors', 'mutators', 'casts', 'observers', 'model-events', 'raw-sql', 'n+1'],
                        'has_playground' => true,
                        'playground_starter_code' => <<<'PHP'
<?php
declare(strict_types=1);

// Sketch of an Order model showing accessor, cast, scope and observer
// patterns side by side. In a real Laravel app each piece lives in its
// own file/class; here they're inlined so you can see the relationships.

class OrderStatus
{
    public const Pending  = 'pending';
    public const Paid     = 'paid';
    public const Refunded = 'refunded';
}

// In a real Laravel app: app/Models/Order.php
// class Order extends Model
// {
//     // Cast: column → typed property automatically
//     protected $casts = [
//         'status'   => OrderStatus::class,  // string-backed enum
//         'metadata' => 'array',             // JSON column → PHP array
//         'paid_at'  => 'immutable_datetime',
//     ];
//
//     // Accessor: derived property without a column
//     public function getTotalEurosAttribute(): float
//     {
//         return $this->total_cents / 100;
//     }
//
//     // Local scope: reusable WHERE clause
//     public function scopeFinal(Builder $q): void
//     {
//         $q->whereIn('status', [OrderStatus::Paid, OrderStatus::Refunded]);
//     }
// }
//
// Usage from a controller:
// Order::final()->where('paid_at', '>', now()->subDays(7))->get();

echo "Patterns compile OK\n";
PHP,
                        'concept_content' => <<<'EOT'
## Core (foundations)

### Query scopes: reusable WHERE clauses

A *local scope* gives a piece of query logic a name. Instead of repeating `where('status', 'paid')` in five controllers, you write it once on the model:

```php
class Order extends Model
{
    public function scopePaid(Builder $query): void
    {
        $query->where('status', 'paid');
    }

    public function scopeForCustomer(Builder $query, int $customerId): void
    {
        $query->where('customer_id', $customerId);
    }
}

// At the call site, the `scope` prefix is dropped:
Order::paid()->forCustomer(42)->get();
```

Two real wins:
- **Refactor-safe.** Rename the `status` column once and every scope-using query updates.
- **Composable.** Chain scopes the same way you'd chain query-builder methods.

Global scopes apply to *every* query on the model (`Builder::addGlobalScope`). Use them for soft-deletes, multi-tenancy, or any "always filter by tenant_id" rule. Strip them per-query with `withoutGlobalScope(...)` when you need to.

### Accessors and casts

A **cast** turns a database column into a richer PHP type *automatically*. You declare it once on the model and every read/write goes through the conversion:

```php
class Order extends Model
{
    protected $casts = [
        'status'     => OrderStatus::class,        // backed enum
        'metadata'   => 'array',                   // JSON ↔ PHP array
        'paid_at'    => 'immutable_datetime',      // DATETIME ↔ CarbonImmutable
        'total_cents'=> 'integer',
    ];
}

$order = Order::find(1);
$order->status;          // OrderStatus::Paid (not a string)
$order->metadata['ip'];  // works — already decoded
```

An **accessor** is a *derived* property that doesn't live in the database — it's computed every time you read it:

```php
class Order extends Model
{
    protected function totalEuros(): Attribute
    {
        return Attribute::get(fn () => $this->total_cents / 100);
    }
}

$order->total_euros;  // float, calculated on the fly
```

Casts for storage shape. Accessors for derived presentation. Don't confuse the two — accessors in JSON responses are great, but accessing them in a `WHERE` clause won't work (they aren't columns).

### Eager loading and N+1

This is the most common Eloquent bug, and it shows up in every code review:

```php
// ❌ N+1: one query for users, then one per user for posts (101 queries for 100 users).
$users = User::all();
foreach ($users as $user) {
    echo $user->name . ': ' . $user->posts->count();
}

// ✅ Eager loaded: 2 queries total.
$users = User::with('posts')->get();
```

`with('relation')` joins-or-batches the related rows in *one* extra query. Use it any time you're going to access a relation in a loop. The N+1 problem is what `withCount()`, `whereHas()`, and `loadMissing()` exist to prevent — learn all three.

## Deeper dive (intermediate Eloquent)

### Model events and observers

Every model emits lifecycle events: `creating`, `created`, `updating`, `updated`, `saving`, `saved`, `deleting`, `deleted`, `retrieved`. You can hook into them with closures or, more sustainably, with an **Observer** class:

```php
class OrderObserver
{
    public function creating(Order $order): void
    {
        $order->reference ??= Str::ulid();
    }

    public function updated(Order $order): void
    {
        if ($order->wasChanged('status') && $order->status === OrderStatus::Paid) {
            event(new OrderPaid($order));
        }
    }
}

// Register in a Service Provider's boot():
Order::observe(OrderObserver::class);
```

Why observers beat scattered model logic:
- **One place** for "what happens when an order is created". A new dev grep's the observer; they don't trawl through six places that all call `Order::create()`.
- **Mass operations bypass observers** (`Order::query()->update()`, `Order::query()->delete()`). That's both a feature (skip the side effects when you want a fast bulk update) and a footgun (forget that, ship a bug). When you intentionally bypass, drop a comment explaining why.

### Custom Casts: when built-ins aren't enough

Sometimes a column needs a non-standard PHP shape. Built-in casts cover scalars, JSON, dates, enums. For everything else, write a **custom cast**:

```php
class EuroMoneyCast implements CastsAttributes
{
    public function get($model, $key, $value, $attributes): Money
    {
        return new Money((int) $value, 'EUR');
    }

    public function set($model, $key, $value, $attributes): int
    {
        return $value instanceof Money ? $value->cents : (int) $value;
    }
}

// On the model:
protected $casts = ['total' => EuroMoneyCast::class];
```

The two-way conversion happens *every time* you touch the attribute. Use this for value objects — `Money`, `Coordinate`, `Range` — anything that's "a thing", not just a primitive.

### When to drop down to raw SQL

Eloquent is a sharp tool for CRUD. It's a terrible tool for:

- **Bulk operations.** `Order::query()->where(...)->update(...)` is fine. `$orders->each(fn ($o) => $o->update(...))` runs 1000 separate queries.
- **Window functions, CTEs, recursive queries.** The query builder doesn't speak `WITH RECURSIVE`. Use `DB::statement()` or write a stored procedure.
- **Analytics queries.** A report that joins 5 tables, groups by 3 dimensions and aggregates 10 metrics: do it in raw SQL or a database view. Eloquent will hydrate millions of Model objects you don't need.

Use `DB::raw()` for column expressions inside a builder:

```php
Order::select('region', DB::raw('SUM(total_cents) as total'))
    ->groupBy('region')
    ->get();
```

And `DB::select(...)` for an entirely hand-written query. Knowing when *not* to use Eloquent is the seniorest thing you can know about Eloquent.

## Senior insights (architecture & code review)

### Code-review patterns to call out

Things a senior would flag on a PR involving Eloquent:

- **`->get()` without `->select(['col1', 'col2'])`.** A `Model::all()` reads every column of every row. On a wide table with TEXT/JSON columns, that's hundreds of kB per row that never reach the response.
- **`$model->relation->...` in a Blade loop without `@foreach ($models->with('relation'))` upstream.** Classic N+1 hidden behind a template.
- **Mass assignment of `$request->all()`.** Always pass a *validated* array. `$model->fill($request->validated())` is the senior pattern.
- **`Model::find($id)` instead of `findOrFail($id)`.** The difference between a 404 and a NullPointer-style crash three layers deep.
- **Querying inside accessors.** `getNameAttribute()` that does `Country::find($this->country_id)->name` runs a query on every property access. It's silent N+1 on steroids.

### Performance: the `chunk` family

A standard `Order::all()->each(...)` loads every row into memory at once. For tables with millions of rows, that OOMs your worker. Use `chunk`:

```php
Order::query()->chunk(500, function ($orders) {
    foreach ($orders as $order) { /* process */ }
});
```

`chunk` pages through the result set in 500-row batches. `chunkById()` is safer if the table is mutating while you read (uses `id > last_id` instead of `OFFSET`). `lazy()` and `lazyById()` give you a `Generator` for the same pattern with cleaner syntax.

### Multi-tenancy gotchas

Tenant isolation usually rides on a global scope:

```php
static::addGlobalScope('tenant', fn ($q) => $q->where('tenant_id', auth()->user()->tenant_id));
```

What goes wrong:

- **Background jobs.** The auth guard isn't bound in queue workers — `auth()->user()` is null and your scope filters everything out. Workers need an explicit `actAs($tenant)` helper that binds tenant context.
- **Migration commands.** Same problem. Run with `--no-interaction` or scope-aware tenant commands.
- **`withoutGlobalScope`.** Calling it once "to do something quick in admin" disables tenant isolation. Make those calls grep-able (`withoutTenantScope` wrapper) so security review can audit them.

### Interview question: *"How would you optimise an Eloquent query that's slow?"*

A senior answer walks the staircase, not the elevator:

1. **EXPLAIN the query.** Look at the index usage. Half the time the column you're filtering on doesn't have an index.
2. **Look for N+1.** Run with the Laravel debug bar (or Telescope). One query in the controller, fifty in the Blade template means you missed `with()`.
3. **Profile column count.** Is `SELECT *` reading TEXT/JSON columns you don't need? Add an explicit `select()`.
4. **Reconsider Eloquent itself.** If you're aggregating across half a million rows, raw SQL or a database view is the answer. Hydration cost is real.
5. **Cache the result.** If the query is read-heavy and rarely changes, `Cache::remember(...)` it. But only after you've fixed the underlying query — caching a slow query just spreads the cost across the cache TTL.

If the candidate jumps straight to "I'd add Redis", they're optimising the wrong layer.
EOT,
                        'resources' => [
                            ['label' => 'Eloquent Scopes', 'url' => 'https://laravel.com/docs/eloquent#query-scopes'],
                            ['label' => 'Eloquent Mutators & Casting', 'url' => 'https://laravel.com/docs/eloquent-mutators'],
                            ['label' => 'Model Events & Observers', 'url' => 'https://laravel.com/docs/eloquent#events'],
                            ['label' => 'Eloquent chunk + lazy', 'url' => 'https://laravel.com/docs/eloquent#chunking-results'],
                        ],
                        'instructions' => [
                            [
                                'id' => 1,
                                'text' => "Extract a scope — find a controller where the same Eloquent where() is repeated across two or more actions. Move it to a scope on the model and refactor the call sites. Confirm `Model::yourScope()` produces the same SQL via toSql().",
                                'starter_code' => <<<'PHP'
<?php
declare(strict_types=1);

// Take this repetitive controller code and refactor `where('status', 'active')->where('paid_at', '!=', null)`
// into a single scope `active()` on the model.

class OrderController
{
    public function index()
    {
        return Order::where('status', 'active')->where('paid_at', '!=', null)->paginate(20);
    }

    public function export()
    {
        return Order::where('status', 'active')->where('paid_at', '!=', null)->get()->toCsv();
    }
}

// TODO: on the Order model, define scopeActive(Builder $query): void
// TODO: rewrite both controller actions to call Order::active() instead.
PHP,
                            ],
                            [
                                'id' => 2,
                                'text' => "Spot the N+1 — given the snippet below, identify how many DB queries it issues. Fix it with the right with() call so it issues exactly two queries regardless of the number of orders.",
                                'starter_code' => <<<'PHP'
<?php
declare(strict_types=1);

// N+1 bug: one query for orders + one per order for customer.name.
// 100 orders → 101 queries. Add a `with()` to fix it.

$orders = Order::where('status', 'paid')->get();

foreach ($orders as $order) {
    echo $order->customer->name . ' bought ' . $order->total_cents . PHP_EOL;
}

// TODO: rewrite the first line so the loop above runs in 2 queries total.
PHP,
                            ],
                            [
                                'id' => 3,
                                'text' => "Build a backed-enum cast — define an OrderStatus enum (pending/paid/refunded) and cast it on the Order model. Then prove with a tinker session that \$order->status === OrderStatus::Paid works (not a string compare).",
                                'starter_code' => <<<'PHP'
<?php
declare(strict_types=1);

enum OrderStatus: string
{
    case Pending  = 'pending';
    case Paid     = 'paid';
    case Refunded = 'refunded';
}

// In a real Order model you would add:
// protected $casts = [
//     'status' => OrderStatus::class,
// ];
//
// Then this should evaluate to true (using === — not ==):

$status = OrderStatus::Paid;
var_dump($status === OrderStatus::Paid);            // true
var_dump($status === 'paid');                        // false — different types
var_dump(OrderStatus::tryFrom('paid') === $status);  // true
PHP,
                            ],
                            [
                                'id' => 4,
                                'text' => "Write an Observer — implement OrderObserver with a `creating` hook that fills `reference` with a ULID when null, and an `updated` hook that fires an OrderPaid event when status changes to Paid. Register it in a Service Provider's boot().",
                                'starter_code' => <<<'PHP'
<?php
declare(strict_types=1);

namespace App\Observers;

class OrderObserver
{
    public function creating(/* Order $order */): void
    {
        // TODO: if $order->reference is null, assign a ULID
        // ($order->reference ??= Str::ulid())
    }

    public function updated(/* Order $order */): void
    {
        // TODO: if status was just changed to "paid", fire an OrderPaid event
        // Hint: $order->wasChanged('status') tells you if the column changed in this save.
    }
}

// In your AppServiceProvider::boot():
// Order::observe(OrderObserver::class);
PHP,
                            ],
                            [
                                'id' => 5,
                                'text' => "Senior interview drill — given a report query that joins 6 tables and aggregates 12 metrics over 2 years of data, decide: do you write it in Eloquent, raw SQL, or a database view? Justify the choice in 2 sentences. (Hint: there's a 'right' answer that interviewers listen for — and it's not Eloquent.)",
                                'starter_code' => null,
                            ],
                        ],
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
                        'description' => 'You wrote the code, the tests pass, but the PR sits open for a week. This module covers what a senior actually looks at — naming, single responsibility, dependency direction, fat controllers, names that lie — and how to write reviews that change behaviour without being a jerk.',
                        'tldr' => 'Tests passing is the floor, not the ceiling. A senior reads a diff and asks four questions: "Does the name match the behaviour?", "Is anything doing more than one thing?", "Will the next developer be surprised?", and "What did this change break that we won\'t notice for three months?". Internalise those questions and your PRs stop sitting open.',
                        'estimated_minutes' => 28,
                        'difficulty' => 'intermediate',
                        'prerequisites' => [
                            ['id' => 1, 'title' => 'Laravel Request Lifecycle'],
                            ['id' => 2, 'title' => 'Advanced Eloquent'],
                        ],
                        'concepts' => ['code-review', 'naming', 'single-responsibility', 'fat-controllers', 'cyclomatic-complexity', 'dependency-direction', 'review-communication'],
                        'has_playground' => true,
                        'playground_starter_code' => <<<'PHP'
<?php
declare(strict_types=1);

// A real example of code that ships every week — and fails review for
// at least five reasons. Refactor it in place. The four-question test:
//   1. Does the name match the behaviour?           (process? user?)
//   2. Is anything doing more than one thing?       (this method does six)
//   3. Will the next developer be surprised?        (returns array OR throws OR commits)
//   4. What did this change break for old callers?  (silent contract drift)

class UserController
{
    public function process($request)
    {
        $user = \DB::table('users')->where('email', $request->email)->first();

        if ($user) {
            \DB::table('users')->where('id', $user->id)->update([
                'last_login' => date('Y-m-d H:i:s'),
                'login_count' => $user->login_count + 1,
            ]);

            \Mail::raw('You logged in.', function ($m) use ($user) {
                $m->to($user->email)->subject('Welcome back');
            });

            \Cache::forget('user_dashboard_' . $user->id);

            return [
                'status' => 'ok',
                'user' => $user,
                'token' => bin2hex(random_bytes(16)),
            ];
        }

        return ['status' => 'error'];
    }
}

// Suggested refactor sketch — five smaller methods, each named for its job:
//   - findUserByEmail(string $email): ?User
//   - recordLogin(User $user): void          (last_login + counter)
//   - sendWelcomeBackEmail(User $user): void
//   - invalidateDashboardCache(User $user): void
//   - issueLoginToken(User $user): string
// Then `login()` (NOT `process()`) orchestrates them.
PHP,
                        'concept_content' => <<<'EOT'
## Core (foundations)

### Naming: the name is the contract

Half of all code-review feedback comes down to one question: *does the name match the behaviour?* When it doesn''t, the reader has to mentally translate every call site, which is where bugs hide.

```php
// ❌ Names that lie
function getUser(int $id): User
{
    $user = User::find($id);
    if (! $user) {
        $user = User::create(['id' => $id, 'name' => 'Guest']);  // ← surprise: it creates one
    }
    return $user;
}

// ✅ Name that matches behaviour
function findOrCreateUser(int $id): User { /* ... */ }
```

Rules of thumb:

- `get` / `find` should be cheap and side-effect free.
- `create` / `make` / `build` allocate something new.
- `process` / `handle` / `do` — these are red flags. They tell you nothing about what the function actually does.
- Boolean methods read like questions: `isPaid()`, `hasRefund()`, not `paid()` or `refund()`.

### One method, one job (Single Responsibility)

A method should have **one reason to change**. The reasons aren''t lines of code or branches — they''re the business reasons someone might modify it.

```php
// ❌ Three reasons to change: invoice format, email content, audit log shape.
public function bill(Order $order): void
{
    $pdf = $this->buildInvoicePdf($order);          // reason 1
    Mail::to($order->user)->send(new InvoiceMail($pdf));  // reason 2
    AuditLog::record('order.billed', ['order_id' => $order->id]);  // reason 3
}

// ✅ Three small methods, plus an orchestrator that does only the orchestration.
public function bill(Order $order): void
{
    $this->generateInvoice($order);
    $this->emailInvoice($order);
    $this->auditBilling($order);
}
```

The orchestrator is allowed to be longer — it''s describing the workflow, not implementing it. The implementations sit next to it where you can test each one in isolation.

### The "fat controller" anti-pattern

Controllers are HTTP adapters. Their job is to:

1. Translate request → typed input.
2. Hand it to a service / action / use-case.
3. Translate result → response.

That''s it. Anything else — database calls, mail sending, business rules — belongs elsewhere. When you see a controller method past 30 lines, that''s 30 lines that can''t be reused from a queue worker, a CLI command, or a test.

```php
// ❌ Fat controller
class CheckoutController extends Controller
{
    public function complete(Request $request)
    {
        // 80 lines: validates, charges Stripe, writes Order, emails receipt,
        // dispatches inventory job, updates user_stats…
    }
}

// ✅ Thin controller
class CheckoutController extends Controller
{
    public function complete(CompleteCheckoutRequest $request, CompleteCheckout $action): JsonResponse
    {
        $order = $action->handle($request->validated());

        return new OrderResource($order)->response()->setStatusCode(201);
    }
}
```

The `CompleteCheckout` action is also callable from `php artisan checkout:complete 42` and from a Stripe webhook handler. The fat controller version is callable from exactly one place.

### Names that lie — boolean returns

The single nastiest naming bug is a function whose **name implies a question** but whose **body has side effects**:

```php
public function hasInventory(Product $p): bool
{
    if (! Cache::has("stock.{$p->id}")) {
        Cache::put("stock.{$p->id}", $p->refreshStock());  // ← side effect
    }
    return Cache::get("stock.{$p->id}") > 0;
}
```

That cache prime is hiding inside what looks like a pure query. The next dev writes `if ($product->hasInventory()) { ... }` in a `Promise.all`-style parallel job and the cache stampedes. Split it: `refreshStockIfStale()` for the side effect, `hasInventory()` for the question.

## Deeper dive (intermediate)

### Cyclomatic complexity: just count the ifs

A senior eye can spot a method that''s too complex in about three seconds. The technical name is **cyclomatic complexity** — the number of independent paths through a function. The practical version: count your `if`, `match`, `&&`, `||`, and `?:`. More than 5 and you''re looking at a method that needs to be split.

```php
// CC ≈ 7 — every level of nesting is another path to test.
function calculateDiscount($order)
{
    if ($order->user->isVip()) {
        if ($order->total > 1000) {
            return $order->total * 0.20;
        } elseif ($order->total > 500) {
            return $order->total * 0.15;
        }
        return $order->total * 0.10;
    } else {
        if (now()->isWeekend()) {
            return $order->total * 0.05;
        }
    }
    return 0;
}
```

The refactored version pushes the decisions into a data structure:

```php
function calculateDiscount(Order $order): int
{
    $rules = match (true) {
        $order->user->isVip() && $order->total > 1000 => 0.20,
        $order->user->isVip() && $order->total > 500  => 0.15,
        $order->user->isVip()                          => 0.10,
        now()->isWeekend()                             => 0.05,
        default                                        => 0.00,
    };

    return (int) round($order->total * $rules);
}
```

Same six paths, but every path is on one line and the discount table is now greppable.

### Dependency direction: stable things don''t depend on unstable things

A core rule of architecture: **dependencies should point toward stability**. The hot, changes-every-week parts of your codebase (controllers, CLI commands, schedulers) should depend on the slow, rarely-changing parts (entities, domain services). Not the other way around.

Concretely:

- Your `Order` model should know nothing about HTTP requests.
- Your domain services should accept typed input, not `Illuminate\Http\Request`.
- Your business logic should not call `auth()->user()` — it should accept a `User` parameter.

When dependencies point the wrong way, the symptom is "I can''t test this without spinning up the whole app". When they point the right way, you can unit-test the business logic against plain PHP objects.

### When DRY is the wrong principle

"Don''t Repeat Yourself" is the principle most often weaponised against good code. Three real-world misapplications:

- **Two different rules that happen to look the same.** If the business logic for "VIP discount" and "weekend discount" are calculated the same way today but for unrelated reasons, *don''t* extract them. They''ll drift, and the abstraction that joined them will become tribal knowledge.
- **A shared helper that breaks one caller every time it changes.** This is the "I tried to fix the bug, broke prod for two teams" situation. The shared utility has too many opinionated callers.
- **Inheritance to remove repetition.** `class PaidOrder extends Order` looks DRY. It''s actually the worst kind of repetition because now every change to `Order` is a change to `PaidOrder` you didn''t intend.

Senior heuristic: **prefer duplication over the wrong abstraction**. Three similar functions are easier to fix than one over-general abstraction with eight optional flags.

### The "principle of least surprise"

Code is read 10x more than it''s written. Two equally-correct implementations should be judged by *which one will surprise the next reader less*.

```php
// Surprising: the cache TTL silently shadows the method param.
public function fetch(string $key, int $ttl = 60): mixed
{
    $ttl = config('cache.default_ttl', $ttl);  // ← config wins; param is ignored when set
    return Cache::remember($key, $ttl, ...);
}

// Less surprising: explicit precedence in the name.
public function fetchWithConfigTtl(string $key): mixed { /* ... */ }
public function fetchWithCustomTtl(string $key, int $ttl): mixed { /* ... */ }
```

If you read the surprising version six months from now, you''ll think the `$ttl = 30` you passed is being used. It isn''t. That''s a bug born at write-time and discovered at 2am.

## Senior insights (giving reviews + strategic)

### How to give a code review that actually helps

The hardest part of code review isn''t spotting issues — it''s communicating them so the author wants to fix them. Three habits:

- **Distinguish must-fix from nice-to-have.** Tag every comment with one of: `BLOCKING`, `SUGGESTION`, `QUESTION`, `NIT`. Reviewers who don''t tag end up with a 40-comment PR that looks impossible to address; tagged PRs get merged in a day.
- **Phrase suggestions as questions.** "Did you consider naming this `findOrFetch` so the side effect is in the name?" lands better than "Bad name." even though both want the same change.
- **Approve incrementally.** If the diff is mostly good but has one BLOCKING issue, leave the BLOCKING comment and *approve* the rest. The author feels progress; you also gave a clear bar.

### What to NOT comment on

The most senior reviewers comment on fewer things than the second-most-senior reviewers, not more. Things to skip:

- **Formatting.** That''s the linter''s job. If your project doesn''t have one, fix that, not the diff.
- **Personal style preferences.** "I would have used a match instead of switch" is noise unless there''s a reason the match is meaningfully better here.
- **Pre-existing code in the diff''s context.** Unless the PR is specifically refactoring it, don''t hijack the PR.

Every noise comment makes the next signal comment less effective. Reviewers have a budget.

### Strategic review: what won''t we notice for six months?

After you''ve covered correctness, naming, and structure, ask the long-horizon questions. These are what mid-level reviewers miss and what makes a senior''s review distinctive:

- **Is this change reversible?** A column rename can be backed out in a sprint. A column *removal* requires a multi-deploy migration. Surface the difference.
- **Did this PR shift a load to a hot path?** A new `with('relation')` looks like an N+1 fix; if the relation hydrates 50,000 rows on the index page, you just made the index page 10s slower.
- **Will this still make sense after the next feature?** If the team is about to add multi-region, the `now()` call you''re reviewing should already be `now()->in($region)`. Block the PR on the missing seam, not on its absence today.

### Interview question: *"Walk me through how you''d review this PR."*

A senior answer for a hypothetical 200-line PR:

1. **Read the description first.** What was the author trying to do? If the description doesn''t say, that''s the first comment.
2. **Skim the diff structurally.** Which files changed? A diff that touches a controller + a migration + a queue worker is doing three things, and the PR description should explain why.
3. **Read tests before code.** Tests show what the author considers important. Missing test for the new branch you spot? Note it.
4. **Read the code top-down.** Entry points first (route → controller → service), then the leaves (domain methods, queries).
5. **Run the changed file mentally with the failure case.** "What happens if the API returns 500?" "What happens if the user clicks twice?"
6. **Leave comments grouped by file**, with BLOCKING/SUGGESTION/NIT tags.
7. **Approve or request changes — never both.** Pick a state.

If the candidate skips steps 1, 2, 5 and jumps to "I look for code style", they''re an LGTM-clicker, not a reviewer.
EOT,
                        'resources' => [
                            ['label' => 'Clean Code — Robert C. Martin (summary)', 'url' => 'https://gist.github.com/wojteklu/73c6914cc446146b8b533c0988cf8d29'],
                            ['label' => 'Laravel Best Practices', 'url' => 'https://github.com/alexeymezenin/laravel-best-practices'],
                            ['label' => 'SOLID in PHP — practical examples', 'url' => 'https://www.youtube.com/watch?v=_jDNAcej0JE'],
                            ['label' => 'Google\'s code-review guide', 'url' => 'https://google.github.io/eng-practices/review/reviewer/'],
                        ],
                        'instructions' => [
                            [
                                'id' => 1,
                                'text' => "Refactor the lying name — given findOrCreateUser() that''s declared as getUser(), rename it AND find the three call sites in your own current project where this kind of name lies. Take 60 seconds per call site to decide whether the right fix is renaming the function or splitting it.",
                                'starter_code' => <<<'PHP'
<?php
declare(strict_types=1);

// The function is named getUser but creates one as a side effect.
// Two acceptable refactors:
//   (a) rename to findOrCreateUser — explicit about the side effect
//   (b) split into two functions: findUser(): ?User and createGuest(int $id): User
// Decide which one fits your codebase better and apply it.

class User { public int $id; public string $name; }

function getUser(int $id): User
{
    $user = User::find($id);
    if (! $user) {
        $user = User::create(['id' => $id, 'name' => 'Guest']);
    }
    return $user;
}

// TODO: rename or split. Then audit your current project for three more
// functions whose names don''t match their behaviour. Common offenders:
//   - process(), handle(), do() in controllers
//   - get*() methods that hit the DB and write
//   - is*() / has*() methods with side effects
PHP,
                            ],
                            [
                                'id' => 2,
                                'text' => "Slim down a fat controller — refactor the UserController::process() in the playground above. Extract at least three smaller methods, name them for their behaviour, and rewrite process() as a thin orchestrator. Note in a comment which of those new methods could be reused outside this controller.",
                                'starter_code' => null,
                            ],
                            [
                                'id' => 3,
                                'text' => "Cyclomatic complexity hunt — pick the most-branchy function in your current project (or use the discount example below). Count the ifs/&&/?:/match arms. If you go past 5, refactor with a match() table the way the Deeper dive section does.",
                                'starter_code' => <<<'PHP'
<?php
declare(strict_types=1);

// Cyclomatic complexity ≈ 7. Rewrite as a single match() table so the
// discount rules become greppable data instead of nested branches.

class Order
{
    public int $total = 0;
    public bool $userIsVip = false;
}

function calculateDiscount(Order $order): float
{
    if ($order->userIsVip) {
        if ($order->total > 1000) {
            return $order->total * 0.20;
        } elseif ($order->total > 500) {
            return $order->total * 0.15;
        }
        return $order->total * 0.10;
    } else {
        if (date('N') >= 6) {  // weekend
            return $order->total * 0.05;
        }
    }
    return 0;
}

// TODO: rewrite using `match (true)` with one arm per rule.
// Bonus: extract the rules into a static array so future rules are
// added without touching the function body.
PHP,
                            ],
                            [
                                'id' => 4,
                                'text' => "Write a real code-review comment — paste a 30–80 line piece of code you wrote in the last month into the playground. Comment on it as if you''re reviewing a stranger''s PR. Use BLOCKING / SUGGESTION / NIT tags. If you make more than 10 comments on your own 80 lines, you''ve learned something about your own habits.",
                                'starter_code' => null,
                            ],
                            [
                                'id' => 5,
                                'text' => "Surface a reversibility question — find a PR you opened in the last month. Write one paragraph answering: \"What did this change make harder to undo?\". This is the question that distinguishes senior reviewers from mid-level — train yourself to ask it before the merge button, not after.",
                                'starter_code' => null,
                            ],
                        ],
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
                        'type' => 'reading',
                        'description' => 'Poorly written tests give false confidence and slow down development. We cover the difference between unit, feature and integration tests in Laravel, when to mock vs hit a real database, factory states, the N+1 regression detector, and what to *not* test.',
                        'tldr' => 'A test suite\'s value isn\'t the green checkmark — it\'s the bugs it catches at 3am before a customer does. Feature tests covering real HTTP paths are your highest-leverage tool; mocks should be the exception, not the default; and the most important assertion is the one you skip when you\'re tired (the negative case).',
                        'estimated_minutes' => 26,
                        'difficulty' => 'intermediate',
                        'prerequisites' => [
                            ['id' => 1, 'title' => 'Laravel Request Lifecycle'],
                            ['id' => 2, 'title' => 'Advanced Eloquent'],
                            ['id' => 3, 'title' => 'Code Review: What a Senior Would See'],
                        ],
                        'concepts' => ['feature-tests', 'unit-tests', 'factories', 'states', 'mocks-vs-fakes', 'n+1-detector', 'refresh-database', 'tdd'],
                        'has_playground' => true,
                        'playground_starter_code' => <<<'PHP'
<?php
declare(strict_types=1);

// A Laravel feature test that does the three things every test should do:
//   1. ARRANGE — build the world the test needs (factories with state).
//   2. ACT     — perform one action.
//   3. ASSERT  — check exactly the consequences you care about.
//
// In a real project this lives in tests/Feature/Task/CreateTaskTest.php.

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_task(): void
    {
        // ── ARRANGE
        $user = \App\Models\User::factory()->create();

        // ── ACT
        $response = $this->actingAs($user)->postJson('/api/tasks', [
            'title' => 'Write the test before the code',
            'due_at' => now()->addDay()->toIsoString(),
        ]);

        // ── ASSERT — three layers: HTTP, DB, and shape of returned data
        $response->assertCreated();
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title'   => 'Write the test before the code',
        ]);
        $response->assertJsonPath('data.user_id', $user->id);
    }
}
PHP,
                        'concept_content' => <<<'EOT'
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
EOT,
                        'resources' => [
                            ['label' => 'Laravel Testing', 'url' => 'https://laravel.com/docs/testing'],
                            ['label' => 'Model Factories', 'url' => 'https://laravel.com/docs/eloquent-factories'],
                            ['label' => 'Pest PHP — modern testing', 'url' => 'https://pestphp.com/docs/installation'],
                            ['label' => 'Mocks aren\'t stubs (Martin Fowler)', 'url' => 'https://martinfowler.com/articles/mocksArentStubs.html'],
                        ],
                        'instructions' => [
                            [
                                'id' => 1,
                                'text' => "Configure the test environment — set DB_CONNECTION=sqlite and DB_DATABASE=:memory: in phpunit.xml. Time a 10-test run before and after. The difference is what you''ll save on every CI build for the rest of the project''s life.",
                                'starter_code' => null,
                            ],
                            [
                                'id' => 2,
                                'text' => "Build factories with states — implement TaskFactory with pending() and overdue() states (overdue = due_at < now AND status != done). Use the states in a feature test that asserts the /api/tasks/overdue endpoint returns exactly the overdue rows.",
                                'starter_code' => <<<'PHP'
<?php
declare(strict_types=1);

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title'   => $this->faker->sentence(),
            'status'  => 'pending',
            'due_at'  => now()->addDays(7),
        ];
    }

    public function pending(): self
    {
        // TODO: state for explicitly-pending tasks (status = 'pending').
    }

    public function overdue(): self
    {
        // TODO: state for overdue tasks (due_at in the past AND status != 'done').
    }
}
PHP,
                            ],
                            [
                                'id' => 3,
                                'text' => "Write the three feature tests that protect login: success, wrong password (returns 422), and throttled after five failed attempts (returns 429). Junior tests cover (1); the bugs that ship are (2) and (3).",
                                'starter_code' => <<<'PHP'
<?php
declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_log_in_with_valid_credentials(): void
    {
        // ARRANGE: create a user with a known password
        // ACT:     post valid credentials to /api/login
        // ASSERT:  200, response carries an access_token, DB shows last_login bumped
    }

    public function test_login_rejects_wrong_password(): void
    {
        // TODO: 422 + no token in response + DB unchanged
    }

    public function test_login_throttles_after_five_failed_attempts(): void
    {
        // TODO: 5 failed posts, then the 6th returns 429
    }
}
PHP,
                            ],
                            [
                                'id' => 4,
                                'text' => "Add an N+1 detector — implement the AssertsQueryCount trait from the Deeper dive section. Use it in a feature test for /api/orders that creates 20 orders and asserts the index endpoint runs at most 3 queries. The 4th query that future-you adds for a relation will fail the test instead of fail in prod.",
                                'starter_code' => <<<'PHP'
<?php
declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Support\Facades\DB;

trait AssertsQueryCount
{
    protected function assertQueryCountAtMost(int $max, callable $action): void
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $action();

        // TODO: count DB::getQueryLog() and assertLessThanOrEqual($max, $count)
        // with a helpful message including the actual count.
    }
}

// Usage in tests/Feature/Order/IndexTest.php:
//
// use AssertsQueryCount;
//
// public function test_index_does_not_n_plus_one(): void
// {
//     Order::factory()->for(User::factory())->count(20)->create();
//
//     $this->assertQueryCountAtMost(3, function () {
//         $this->getJson('/api/orders')->assertOk();
//     });
// }
PHP,
                            ],
                            [
                                'id' => 5,
                                'text' => "Senior interview drill — write a three-sentence answer to \"What''s your testing strategy?\" *without* mentioning coverage percentage. If you can''t avoid the word coverage, you''re measuring the wrong thing and an interviewer will catch it.",
                                'starter_code' => null,
                            ],
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
