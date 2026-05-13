<?php

namespace Database\Seeders;

use App\Enums\ChallengeDifficulty;
use App\Models\Challenge;
use App\Models\User;
use Illuminate\Database\Seeder;

class ChallengeSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::find(1);

        if (! $admin) {
            return;
        }

        foreach ($this->challenges() as $data) {
            Challenge::updateOrCreate(
                ['slug' => $data['slug']],
                [...$data, 'created_by' => $admin->id],
            );
        }
    }

    private function challenges(): array
    {
        return [
            // ─────────────────────────────────────────────────────────────
            // BEGINNER
            // ─────────────────────────────────────────────────────────────
            [
                'title' => 'Null-Safe Property Chain',
                'slug' => 'null-safe-property-chain',
                'difficulty' => ChallengeDifficulty::Beginner,
                'description' => <<<'MD'
## Null-Safe Property Chain

PHP 8.0 introduced the **null-safe operator** `?->` to short-circuit a chain when any member is `null` instead of throwing an error.

**Goal:** implement `getCity()` so it returns the city name from a nested object graph — or `null` if any link in the chain is absent. Use a single expression with `?->`.

```php
$user?->address?->city?->name
```
MD,
                'boilerplate_code' => <<<'PHP'
<?php

class City
{
    public function __construct(public string $name) {}
}

class Address
{
    public function __construct(public ?City $city = null) {}
}

class User
{
    public function __construct(public ?Address $address = null) {}
}

function getCity(?User $user): ?string
{
    // TODO: return the city name using the null-safe operator,
    // or null if any part of the chain is absent.
}
PHP,
                'tests_code' => <<<'PHP'
<?php

use PHPUnit\Framework\TestCase;

class NullSafeTest extends TestCase
{
    public function test_returns_city_name_when_full_chain_exists(): void
    {
        $user = new User(new Address(new City('Dublin')));
        $this->assertSame('Dublin', getCity($user));
    }

    public function test_returns_null_when_user_is_null(): void
    {
        $this->assertNull(getCity(null));
    }

    public function test_returns_null_when_address_is_null(): void
    {
        $this->assertNull(getCity(new User(null)));
    }

    public function test_returns_null_when_city_is_null(): void
    {
        $this->assertNull(getCity(new User(new Address(null))));
    }
}
PHP,
                'is_premium' => false,
                'price_eur' => null,
            ],

            [
                'title' => 'HTTP Status Labels with match',
                'slug' => 'http-status-match',
                'difficulty' => ChallengeDifficulty::Beginner,
                'description' => <<<'MD'
## HTTP Status Labels with match

PHP 8.0 **match expressions** are the clean replacement for `switch`. Unlike `switch`, `match` uses strict comparison, returns a value directly, and throws `\UnhandledMatchError` for unmatched values — no more accidental fall-through.

**Goal:** implement `statusLabel()` using a `match` expression. Do **not** add a `default` arm — let unknown codes throw automatically.

| Code | Text |
|------|------|
| 200 | `OK` |
| 201 | `Created` |
| 400 | `Bad Request` |
| 401 | `Unauthorized` |
| 403 | `Forbidden` |
| 404 | `Not Found` |
| 422 | `Unprocessable Entity` |
| 500 | `Internal Server Error` |
MD,
                'boilerplate_code' => <<<'PHP'
<?php

function statusLabel(int $code): string
{
    // TODO: use a match expression.
    // Do NOT add a default arm — let unknown codes throw \UnhandledMatchError.
}
PHP,
                'tests_code' => <<<'PHP'
<?php

use PHPUnit\Framework\TestCase;

class HttpStatusMatchTest extends TestCase
{
    public function test_200_ok(): void
    {
        $this->assertSame('OK', statusLabel(200));
    }

    public function test_201_created(): void
    {
        $this->assertSame('Created', statusLabel(201));
    }

    public function test_400_bad_request(): void
    {
        $this->assertSame('Bad Request', statusLabel(400));
    }

    public function test_401_unauthorized(): void
    {
        $this->assertSame('Unauthorized', statusLabel(401));
    }

    public function test_403_forbidden(): void
    {
        $this->assertSame('Forbidden', statusLabel(403));
    }

    public function test_404_not_found(): void
    {
        $this->assertSame('Not Found', statusLabel(404));
    }

    public function test_422_unprocessable(): void
    {
        $this->assertSame('Unprocessable Entity', statusLabel(422));
    }

    public function test_500_internal_server_error(): void
    {
        $this->assertSame('Internal Server Error', statusLabel(500));
    }

    public function test_unknown_code_throws(): void
    {
        $this->expectException(\UnhandledMatchError::class);
        statusLabel(418);
    }
}
PHP,
                'is_premium' => false,
                'price_eur' => null,
            ],

            // ─────────────────────────────────────────────────────────────
            // INTERMEDIATE
            // ─────────────────────────────────────────────────────────────
            [
                'title' => 'Status Enum — Label & Color',
                'slug' => 'status-enum-label-color',
                'difficulty' => ChallengeDifficulty::Intermediate,
                'description' => <<<'MD'
## Status Enum — Label & Color

PHP 8.1 **backed enums** can carry methods that centralise display logic, keeping `match` expressions out of view templates.

**Goal:** complete the `OrderStatus` enum so that each case returns a human-readable `label()` and a Tailwind CSS `color()` string.

| Case | Label | Color |
|------|-------|-------|
| `Pending` | `"Pending payment"` | `"yellow"` |
| `Paid` | `"Paid"` | `"green"` |
| `Cancelled` | `"Cancelled"` | `"red"` |
MD,
                'boilerplate_code' => <<<'PHP'
<?php

enum OrderStatus: string
{
    case Pending   = 'pending';
    case Paid      = 'paid';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        // TODO: return human-readable label for each case
    }

    public function color(): string
    {
        // TODO: return Tailwind color name: "yellow", "green", or "red"
    }
}
PHP,
                'tests_code' => <<<'PHP'
<?php

use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    public function test_pending_label_and_color(): void
    {
        $this->assertSame('Pending payment', OrderStatus::Pending->label());
        $this->assertSame('yellow', OrderStatus::Pending->color());
    }

    public function test_paid_label_and_color(): void
    {
        $this->assertSame('Paid', OrderStatus::Paid->label());
        $this->assertSame('green', OrderStatus::Paid->color());
    }

    public function test_cancelled_label_and_color(): void
    {
        $this->assertSame('Cancelled', OrderStatus::Cancelled->label());
        $this->assertSame('red', OrderStatus::Cancelled->color());
    }

    public function test_from_string_returns_correct_case(): void
    {
        $this->assertSame(OrderStatus::Paid, OrderStatus::from('paid'));
    }
}
PHP,
                'is_premium' => false,
                'price_eur' => null,
            ],

            [
                'title' => 'Null Safety in Production',
                'slug' => 'null-safety-in-production',
                'difficulty' => ChallengeDifficulty::Intermediate,
                'description' => <<<'MD'
## Null Safety in Production

In observability platforms like New Relic, a **transaction** carries an optional **span** that holds custom attributes. Reading a missing attribute with a direct property access causes a fatal error — crashing your monitoring instrumentation at the worst possible time.

**Goal:** rewrite `getCustomAttributeValue()` so it safely returns `null` when either the transaction has no active span, or the span does not carry the requested attribute.

Use the **null-safe operator** `?->` to short-circuit the chain without conditionals.

```php
// Before — crashes with a fatal error
return $transaction->currentSpan->getAttribute($name)->value;

// After — returns null safely
return $transaction->currentSpan?->getAttribute($name)?->value;
```

**Why it matters:** in production, spans are created and destroyed across async boundaries. Assuming a span always exists is a guaranteed outage waiting to happen.
MD,
                'boilerplate_code' => <<<'PHP'
<?php

class Attribute
{
    public function __construct(
        public string $name,
        public mixed $value,
    ) {}
}

class Span
{
    /** @param array<string, Attribute> $attributes */
    public function __construct(
        private array $attributes = [],
    ) {}

    public function getAttribute(string $name): ?Attribute
    {
        return $this->attributes[$name] ?? null;
    }
}

class Transaction
{
    public function __construct(
        public readonly string $id,
        public ?Span $currentSpan = null,
    ) {}
}

function getCustomAttributeValue(Transaction $transaction, string $name): mixed
{
    // BUG: crashes with a fatal error when currentSpan is null
    // or when the attribute does not exist on the span.
    // Fix this using the null-safe operator ?->
    return $transaction->currentSpan->getAttribute($name)->value;
}
PHP,
                'tests_code' => <<<'PHP'
<?php

use PHPUnit\Framework\TestCase;

class NullSafetyInProductionTest extends TestCase
{
    public function test_returns_value_when_attribute_exists(): void
    {
        $span = new Span(['user_id' => new Attribute('user_id', 42)]);
        $tx   = new Transaction('tx-001', $span);
        $this->assertSame(42, getCustomAttributeValue($tx, 'user_id'));
    }

    public function test_returns_null_when_span_is_null(): void
    {
        $tx = new Transaction('tx-002', null);
        $this->assertNull(getCustomAttributeValue($tx, 'user_id'));
    }

    public function test_returns_null_when_attribute_is_missing(): void
    {
        $span = new Span([]);
        $tx   = new Transaction('tx-003', $span);
        $this->assertNull(getCustomAttributeValue($tx, 'user_id'));
    }

    public function test_returns_string_attribute_value(): void
    {
        $span = new Span(['route' => new Attribute('route', '/api/users')]);
        $tx   = new Transaction('tx-004', $span);
        $this->assertSame('/api/users', getCustomAttributeValue($tx, 'route'));
    }

    public function test_returns_null_for_wrong_attribute_name(): void
    {
        $span = new Span(['host' => new Attribute('host', 'prod-1')]);
        $tx   = new Transaction('tx-005', $span);
        $this->assertNull(getCustomAttributeValue($tx, 'region'));
    }
}
PHP,
                'is_premium' => false,
                'price_eur' => null,
            ],

            [
                'title' => 'Array Pipeline: Filter, Map, Sort',
                'slug' => 'array-pipeline-filter-map-sort',
                'difficulty' => ChallengeDifficulty::Intermediate,
                'description' => <<<'MD'
## Array Pipeline: Filter, Map, Sort

Functional array operations (`array_filter`, `array_map`, `usort`) are the backbone of data transformation in PHP. Chaining them replaces loops with readable, composable expressions.

**Goal:** implement `formatPremiumProducts()` that:
1. **Filters** out inactive products and products with `price_cents` ≤ 5000 (i.e. ≤ €50.00)
2. **Maps** each remaining product to the string `"Name — €X.XX"` (two decimal places)
3. **Sorts** the result alphabetically by product name

```php
// Example input:
$products = [
    ['name' => 'Widget B', 'price_cents' => 15000, 'active' => true],
    ['name' => 'Widget A', 'price_cents' => 3000,  'active' => true],   // too cheap
    ['name' => 'Widget C', 'price_cents' => 20000, 'active' => false],  // inactive
    ['name' => 'Widget D', 'price_cents' => 30000, 'active' => true],
];
// Expected: ['Widget B — €150.00', 'Widget D — €300.00']
```
MD,
                'boilerplate_code' => <<<'PHP'
<?php

/**
 * @param  array<array{name: string, price_cents: int, active: bool}> $products
 * @return list<string>
 */
function formatPremiumProducts(array $products): array
{
    // TODO:
    // 1. Filter: active === true AND price_cents > 5000
    // 2. Map:    format as "Name — €X.XX"
    // 3. Sort:   alphabetically by name (ascending)
    // Return a re-indexed list of strings.
}
PHP,
                'tests_code' => <<<'PHP'
<?php

use PHPUnit\Framework\TestCase;

class ArrayPipelineTest extends TestCase
{
    private array $catalogue = [
        ['name' => 'Widget B', 'price_cents' => 15000, 'active' => true],
        ['name' => 'Widget A', 'price_cents' => 3000,  'active' => true],
        ['name' => 'Widget C', 'price_cents' => 20000, 'active' => false],
        ['name' => 'Widget D', 'price_cents' => 30000, 'active' => true],
        ['name' => 'Widget E', 'price_cents' => 5000,  'active' => true],
    ];

    public function test_returns_only_active_products_above_threshold(): void
    {
        $result = formatPremiumProducts($this->catalogue);
        $this->assertCount(2, $result);
    }

    public function test_formats_price_as_euros_with_two_decimals(): void
    {
        $result = formatPremiumProducts($this->catalogue);
        $this->assertContains('Widget B — €150.00', $result);
        $this->assertContains('Widget D — €300.00', $result);
    }

    public function test_result_is_sorted_alphabetically(): void
    {
        $result = formatPremiumProducts($this->catalogue);
        $this->assertSame(['Widget B — €150.00', 'Widget D — €300.00'], $result);
    }

    public function test_filters_out_inactive_products(): void
    {
        $result = formatPremiumProducts($this->catalogue);
        foreach ($result as $label) {
            $this->assertStringNotContainsString('Widget C', $label);
        }
    }

    public function test_filters_out_products_at_threshold(): void
    {
        $result = formatPremiumProducts($this->catalogue);
        foreach ($result as $label) {
            $this->assertStringNotContainsString('Widget E', $label);
        }
    }

    public function test_returns_empty_array_for_empty_input(): void
    {
        $this->assertSame([], formatPremiumProducts([]));
    }
}
PHP,
                'is_premium' => false,
                'price_eur' => null,
            ],

            [
                'title' => 'Readonly Money Value Object',
                'slug' => 'readonly-money-value-object',
                'difficulty' => ChallengeDifficulty::Intermediate,
                'description' => <<<'MD'
## Readonly Money Value Object

PHP 8.1 **readonly properties** enforce immutability at the language level. Combined with constructor promotion, they make Value Objects concise and safe — mutation returns a new instance rather than modifying the original.

**Goal:** implement three methods on the `Money` class:
- `add(Money $other): Money` — returns a **new** `Money` with the combined amounts. Throw `\InvalidArgumentException` if the currencies differ.
- `toFloat(): float` — returns `amount / 100` (cents to major units).
- `format(): string` — returns `"€12.50"` for `EUR` and `"R$12.50"` for `BRL`.

```php
$a = new Money(1000, 'EUR'); // €10.00
$b = new Money(500,  'EUR'); // €5.00
$c = $a->add($b);            // new Money(1500, 'EUR')
// $a and $b are unchanged — readonly enforces this
```
MD,
                'boilerplate_code' => <<<'PHP'
<?php

class Money
{
    public function __construct(
        public readonly int $amount,      // in cents
        public readonly string $currency, // 'EUR' or 'BRL'
    ) {}

    public function add(Money $other): self
    {
        // TODO: return a new Money with combined amounts.
        // Throw \InvalidArgumentException if currencies differ.
    }

    public function toFloat(): float
    {
        // TODO: return amount / 100
    }

    public function format(): string
    {
        // TODO: '€' prefix for EUR, 'R$' prefix for BRL, two decimal places.
    }
}
PHP,
                'tests_code' => <<<'PHP'
<?php

use PHPUnit\Framework\TestCase;

class MoneyValueObjectTest extends TestCase
{
    public function test_add_returns_new_instance_with_correct_amount(): void
    {
        $a      = new Money(1000, 'EUR');
        $b      = new Money(500,  'EUR');
        $result = $a->add($b);

        $this->assertInstanceOf(Money::class, $result);
        $this->assertSame(1500, $result->amount);
    }

    public function test_add_does_not_mutate_original(): void
    {
        $a = new Money(1000, 'EUR');
        $b = new Money(500,  'EUR');
        $a->add($b);

        $this->assertSame(1000, $a->amount);
    }

    public function test_add_throws_for_mismatched_currencies(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new Money(1000, 'EUR'))->add(new Money(500, 'BRL'));
    }

    public function test_to_float_converts_cents_to_major_units(): void
    {
        $this->assertSame(12.5, (new Money(1250, 'EUR'))->toFloat());
    }

    public function test_format_eur(): void
    {
        $this->assertSame('€10.00', (new Money(1000, 'EUR'))->format());
    }

    public function test_format_brl(): void
    {
        $this->assertSame('R$99.90', (new Money(9990, 'BRL'))->format());
    }
}
PHP,
                'is_premium' => false,
                'price_eur' => null,
            ],

            [
                'title' => 'Functional Pipe — Left-to-Right Composition',
                'slug' => 'functional-pipe-composition',
                'difficulty' => ChallengeDifficulty::Intermediate,
                'description' => <<<'MD'
## Functional Pipe — Left-to-Right Composition

Function composition is the foundation of functional programming. A `pipe()` function applies a list of callables in sequence, passing the output of each as the input of the next.

**Goal:** implement `pipe()` using `array_reduce`. It must:
- Accept any number of callables via variadic `...$fns`
- Apply them **left to right**: `pipe(f, g, h)($x)` === `h(g(f($x)))`
- Return an identity function when called with no arguments: `pipe()($x)` === `$x`

```php
$transform = pipe(
    fn(string $s) => strtolower($s),
    fn(string $s) => trim($s),
    fn(string $s) => str_replace(' ', '-', $s),
);

$transform('  Hello World  '); // → 'hello-world'
```
MD,
                'boilerplate_code' => <<<'PHP'
<?php

/**
 * Returns a new callable that applies $fns left-to-right.
 * pipe(f, g, h)($x) === h(g(f($x)))
 * pipe()($x)        === $x   (identity)
 */
function pipe(callable ...$fns): callable
{
    // TODO: use array_reduce to compose the functions.
}
PHP,
                'tests_code' => <<<'PHP'
<?php

use PHPUnit\Framework\TestCase;

class FunctionalPipeTest extends TestCase
{
    public function test_single_function_is_applied(): void
    {
        $double = fn(int $n) => $n * 2;
        $this->assertSame(10, pipe($double)(5));
    }

    public function test_two_functions_applied_left_to_right(): void
    {
        $addOne  = fn(int $n) => $n + 1;
        $double  = fn(int $n) => $n * 2;
        // pipe(addOne, double)(3) = double(addOne(3)) = double(4) = 8
        $this->assertSame(8, pipe($addOne, $double)(3));
    }

    public function test_three_functions_applied_in_order(): void
    {
        $lower  = fn(string $s) => strtolower($s);
        $trim   = fn(string $s) => trim($s);
        $slug   = fn(string $s) => str_replace(' ', '-', $s);

        $this->assertSame('hello-world', pipe($lower, $trim, $slug)('  Hello World  '));
    }

    public function test_empty_pipe_returns_identity(): void
    {
        $this->assertSame(42, pipe()(42));
        $this->assertSame('foo', pipe()('foo'));
    }

    public function test_works_with_builtin_callables(): void
    {
        $this->assertSame(5, pipe('strlen')('hello'));
    }
}
PHP,
                'is_premium' => false,
                'price_eur' => null,
            ],

            // ─────────────────────────────────────────────────────────────
            // ADVANCED
            // ─────────────────────────────────────────────────────────────
            [
                'title' => 'Enum-Driven State Machine',
                'slug' => 'enum-driven-state-machine',
                'difficulty' => ChallengeDifficulty::Advanced,
                'description' => <<<'MD'
## Enum-Driven State Machine

Combine PHP 8.1 **enums** with **readonly properties** to build a lightweight state machine. Valid transitions are encoded directly on the enum, making illegal transitions impossible to overlook.

**Goal:** implement `transitions()` on `InvoiceStatus` returning the allowed next states, and `transitionTo()` on `Invoice` that:
- returns a new `Invoice` with the updated status if the transition is valid,
- returns `null` if the transition is forbidden.

Valid transitions:
- `Draft` → `Sent`
- `Sent` → `Paid` or `Cancelled`
- `Paid` and `Cancelled` are terminal (no transitions)
MD,
                'boilerplate_code' => <<<'PHP'
<?php

enum InvoiceStatus: string
{
    case Draft     = 'draft';
    case Sent      = 'sent';
    case Paid      = 'paid';
    case Cancelled = 'cancelled';

    /** @return list<self> */
    public function transitions(): array
    {
        // TODO: return allowed next states for each case
    }
}

class Invoice
{
    public function __construct(
        public readonly int $id,
        public readonly InvoiceStatus $status,
    ) {}

    public function transitionTo(InvoiceStatus $next): ?static
    {
        // TODO: return a new Invoice with $next status if allowed,
        // or null if the transition is forbidden
    }
}
PHP,
                'tests_code' => <<<'PHP'
<?php

use PHPUnit\Framework\TestCase;

class StateMachineTest extends TestCase
{
    public function test_draft_can_transition_to_sent(): void
    {
        $invoice = new Invoice(1, InvoiceStatus::Draft);
        $next    = $invoice->transitionTo(InvoiceStatus::Sent);
        $this->assertNotNull($next);
        $this->assertSame(InvoiceStatus::Sent, $next->status);
    }

    public function test_draft_cannot_transition_to_paid(): void
    {
        $invoice = new Invoice(1, InvoiceStatus::Draft);
        $this->assertNull($invoice->transitionTo(InvoiceStatus::Paid));
    }

    public function test_sent_can_transition_to_paid_or_cancelled(): void
    {
        $invoice = new Invoice(2, InvoiceStatus::Sent);
        $this->assertNotNull($invoice->transitionTo(InvoiceStatus::Paid));
        $this->assertNotNull($invoice->transitionTo(InvoiceStatus::Cancelled));
    }

    public function test_paid_is_terminal(): void
    {
        $invoice = new Invoice(3, InvoiceStatus::Paid);
        $this->assertNull($invoice->transitionTo(InvoiceStatus::Cancelled));
    }

    public function test_transition_preserves_id(): void
    {
        $invoice = new Invoice(42, InvoiceStatus::Draft);
        $next    = $invoice->transitionTo(InvoiceStatus::Sent);
        $this->assertSame(42, $next->id);
    }
}
PHP,
                'is_premium' => true,
                'price_eur' => 990,
            ],

            [
                'title' => 'Memory-Efficient Generator Pagination',
                'slug' => 'generator-paginated-rows',
                'difficulty' => ChallengeDifficulty::Advanced,
                'description' => <<<'MD'
## Memory-Efficient Generator Pagination

Loading millions of rows from a database into an array will exhaust memory. PHP **generators** solve this by yielding rows one at a time — the next page is only fetched when the consumer asks for it.

**Goal:** implement `paginatedRows()` as a `Generator` that:
- Calls `$fetchPage(int $page, int $perPage)` starting from page 1
- **Yields** each row from the page individually
- Fetches the next page only after the current page is exhausted
- **Stops** when `$fetchPage` returns an empty array

```php
$rows = paginatedRows(fn($page, $per) => fetchFromDb($page, $per), perPage: 500);
foreach ($rows as $row) {
    process($row); // never holds more than 500 rows in memory
}
```
MD,
                'boilerplate_code' => <<<'PHP'
<?php

/**
 * Yields rows from a paginated source without loading all pages at once.
 *
 * @param  callable(int $page, int $perPage): array<mixed> $fetchPage
 * @return \Generator<int, mixed, void, void>
 */
function paginatedRows(callable $fetchPage, int $perPage = 100): \Generator
{
    // TODO:
    // - Start at page 1.
    // - Yield each row from fetchPage($page, $perPage).
    // - Increment $page and repeat.
    // - Stop when fetchPage returns an empty array.
}
PHP,
                'tests_code' => <<<'PHP'
<?php

use PHPUnit\Framework\TestCase;

class GeneratorPaginationTest extends TestCase
{
    public function test_yields_all_rows_across_pages(): void
    {
        $data = [['a'], ['b'], ['c'], ['d'], ['e']];
        $fetch = function (int $page, int $perPage) use ($data): array {
            $slice = array_slice($data, ($page - 1) * $perPage, $perPage);
            return $slice;
        };

        $result = iterator_to_array(paginatedRows($fetch, perPage: 2), false);
        $this->assertSame($data, $result);
    }

    public function test_stops_on_empty_page(): void
    {
        $calls = 0;
        $fetch = function (int $page) use (&$calls): array {
            $calls++;
            return $page === 1 ? [['x'], ['y']] : [];
        };

        iterator_to_array(paginatedRows($fetch, perPage: 10), false);
        $this->assertSame(2, $calls); // page 1 (data) + page 2 (empty)
    }

    public function test_yields_nothing_for_empty_first_page(): void
    {
        $result = iterator_to_array(paginatedRows(fn() => [], perPage: 10), false);
        $this->assertSame([], $result);
    }

    public function test_passes_correct_arguments_to_fetch(): void
    {
        $received = [];
        $fetch = function (int $page, int $perPage) use (&$received): array {
            $received[] = [$page, $perPage];
            return $page < 3 ? [['row']] : [];
        };

        iterator_to_array(paginatedRows($fetch, perPage: 50), false);
        $this->assertSame([[1, 50], [2, 50], [3, 50]], $received);
    }

    public function test_returns_generator_instance(): void
    {
        $gen = paginatedRows(fn() => [], perPage: 10);
        $this->assertInstanceOf(\Generator::class, $gen);
    }
}
PHP,
                'is_premium' => true,
                'price_eur' => 990,
            ],

            [
                'title' => 'Strategy Pattern: Payment Gateway',
                'slug' => 'strategy-payment-gateway',
                'difficulty' => ChallengeDifficulty::Advanced,
                'description' => <<<'MD'
## Strategy Pattern: Payment Gateway

The **Strategy pattern** separates an algorithm from the code that uses it. The caller does not know which gateway it is talking to — it only knows the contract (`PaymentGateway`). This makes it trivial to add new gateways, mock them in tests, or swap them at runtime based on configuration.

**Goal:** implement the three missing methods:
- `StripeGateway::charge()` — always succeeds, `transactionId` starts with `"stripe_"`
- `PayPalGateway::charge()` — always succeeds, `transactionId` starts with `"paypal_"`
- `PaymentProcessor::process()` — delegates to the injected gateway; if `$amountCents <= 0`, return a failed `PaymentResult` with `error = "Amount must be positive"`

The `PaymentProcessor` must never reference `StripeGateway` or `PayPalGateway` directly — only the interface.
MD,
                'boilerplate_code' => <<<'PHP'
<?php

interface PaymentGateway
{
    public function charge(int $amountCents, string $currency): PaymentResult;
}

class PaymentResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $transactionId,
        public readonly ?string $error = null,
    ) {}
}

class StripeGateway implements PaymentGateway
{
    public function charge(int $amountCents, string $currency): PaymentResult
    {
        // TODO: return successful PaymentResult, transactionId starts with "stripe_"
    }
}

class PayPalGateway implements PaymentGateway
{
    public function charge(int $amountCents, string $currency): PaymentResult
    {
        // TODO: return successful PaymentResult, transactionId starts with "paypal_"
    }
}

class PaymentProcessor
{
    public function __construct(private PaymentGateway $gateway) {}

    public function process(int $amountCents, string $currency): PaymentResult
    {
        // TODO:
        // - If $amountCents <= 0, return failed PaymentResult with error "Amount must be positive"
        // - Otherwise delegate to $this->gateway
    }
}
PHP,
                'tests_code' => <<<'PHP'
<?php

use PHPUnit\Framework\TestCase;

class StrategyPaymentTest extends TestCase
{
    public function test_stripe_gateway_succeeds(): void
    {
        $result = (new StripeGateway())->charge(1000, 'EUR');
        $this->assertTrue($result->success);
        $this->assertStringStartsWith('stripe_', $result->transactionId);
    }

    public function test_paypal_gateway_succeeds(): void
    {
        $result = (new PayPalGateway())->charge(1000, 'EUR');
        $this->assertTrue($result->success);
        $this->assertStringStartsWith('paypal_', $result->transactionId);
    }

    public function test_processor_delegates_to_stripe(): void
    {
        $result = (new PaymentProcessor(new StripeGateway()))->process(500, 'EUR');
        $this->assertTrue($result->success);
        $this->assertStringStartsWith('stripe_', $result->transactionId);
    }

    public function test_processor_delegates_to_paypal(): void
    {
        $result = (new PaymentProcessor(new PayPalGateway()))->process(500, 'EUR');
        $this->assertTrue($result->success);
        $this->assertStringStartsWith('paypal_', $result->transactionId);
    }

    public function test_processor_rejects_zero_amount(): void
    {
        $result = (new PaymentProcessor(new StripeGateway()))->process(0, 'EUR');
        $this->assertFalse($result->success);
        $this->assertSame('Amount must be positive', $result->error);
    }

    public function test_processor_rejects_negative_amount(): void
    {
        $result = (new PaymentProcessor(new StripeGateway()))->process(-100, 'EUR');
        $this->assertFalse($result->success);
    }
}
PHP,
                'is_premium' => true,
                'price_eur' => 1490,
            ],

            // ─────────────────────────────────────────────────────────────
            // EXPERT
            // ─────────────────────────────────────────────────────────────
            [
                'title' => 'Enum-Driven Permission Matrix',
                'slug' => 'enum-permission-matrix',
                'difficulty' => ChallengeDifficulty::Expert,
                'description' => <<<'MD'
## Enum-Driven Permission Matrix

Enterprise applications have complex authorization rules: who can do what, under which conditions, to which resource. Encoding this in a switch statement scattered across controllers is unmaintainable. Encoding it in an enum keeps it all in one place and makes it queryable.

**Goal:** build a `Role` enum whose `can()` method encodes the permission matrix for a document management system:

| Action | `Viewer` | `Editor` | `Admin` |
|--------|----------|----------|---------|
| `read` | ✅ | ✅ | ✅ |
| `write` | ❌ | ✅ | ✅ |
| `delete` | ❌ | ❌ | ✅ |
| `share` | ❌ | ✅ | ✅ |
| `manage_users` | ❌ | ❌ | ✅ |

Also implement `Role::fromString()` that returns the correct case or throws `\ValueError` for unknown roles.
MD,
                'boilerplate_code' => <<<'PHP'
<?php

enum Role: string
{
    case Viewer = 'viewer';
    case Editor = 'editor';
    case Admin  = 'admin';

    /**
     * Returns true if this role has permission to perform $action.
     * Known actions: read, write, delete, share, manage_users
     */
    public function can(string $action): bool
    {
        // TODO: implement the permission matrix using match
    }

    /**
     * Returns the Role for the given string, or throws \ValueError for unknown roles.
     */
    public static function fromString(string $role): self
    {
        // TODO: use self::from() — backed enums do this automatically,
        // but wrap it so callers get \ValueError with a clear message.
    }
}
PHP,
                'tests_code' => <<<'PHP'
<?php

use PHPUnit\Framework\TestCase;

class PermissionMatrixTest extends TestCase
{
    public function test_viewer_can_read(): void
    {
        $this->assertTrue(Role::Viewer->can('read'));
    }

    public function test_viewer_cannot_write(): void
    {
        $this->assertFalse(Role::Viewer->can('write'));
    }

    public function test_viewer_cannot_delete(): void
    {
        $this->assertFalse(Role::Viewer->can('delete'));
    }

    public function test_editor_can_read_write_share(): void
    {
        $this->assertTrue(Role::Editor->can('read'));
        $this->assertTrue(Role::Editor->can('write'));
        $this->assertTrue(Role::Editor->can('share'));
    }

    public function test_editor_cannot_delete_or_manage_users(): void
    {
        $this->assertFalse(Role::Editor->can('delete'));
        $this->assertFalse(Role::Editor->can('manage_users'));
    }

    public function test_admin_can_all_actions(): void
    {
        foreach (['read', 'write', 'delete', 'share', 'manage_users'] as $action) {
            $this->assertTrue(Role::Admin->can($action), "Admin should be able to: $action");
        }
    }

    public function test_from_string_returns_correct_case(): void
    {
        $this->assertSame(Role::Admin, Role::fromString('admin'));
        $this->assertSame(Role::Editor, Role::fromString('editor'));
        $this->assertSame(Role::Viewer, Role::fromString('viewer'));
    }

    public function test_from_string_throws_for_unknown_role(): void
    {
        $this->expectException(\ValueError::class);
        Role::fromString('superuser');
    }
}
PHP,
                'is_premium' => true,
                'price_eur' => 1990,
            ],

            // ─────────────────────────────────────────────────────────────
            // EXERCISE CHALLENGES — linked to PathSteps
            // ─────────────────────────────────────────────────────────────
            [
                'title' => 'Find and Fix the Bugs in This Laravel API',
                'slug' => 'find-fix-laravel-api-bugs',
                'difficulty' => ChallengeDifficulty::Intermediate,
                'description' => <<<'MD'
## Find and Fix the Bugs in This Laravel API

An e-commerce API is in production with critical bugs reported by users. You have access to the code and the logs. No stack trace provided — you need to find, reproduce and fix each problem.

### Bug #1: "Orders appear duplicated at checkout"

When a user submits an order, it sometimes appears twice in the order history. The checkout endpoint has a race condition.

### Bug #2: "Admin can delete their own account"

The admin endpoint doesn't check if the user is trying to delete their own account. This is a critical authorization bug.

### Bug #3: "API returns 500 when product is out of stock"

When a product is out of stock, instead of returning a proper 422 validation error, the API crashes with a 500 Internal Server Error.

**Goal:** Fix all three bugs. The tests will verify each fix.
MD,
                'boilerplate_code' => <<<'PHP'
<?php

class Product
{
    public function __construct(
        public int $id,
        public string $name,
        public int $stock,
    ) {}

    public function isInStock(): bool
    {
        // BUG #3: this returns true even when stock is 0
        return $this->stock >= 0;
    }

    public function reduceStock(int $quantity): void
    {
        $this->stock -= $quantity;
    }
}

class Order
{
    private static int $nextId = 1;
    public int $id;
    public array $items;

    public function __construct()
    {
        $this->id = self::$nextId++;
        $this->items = [];
    }

    public function addItem(Product $product, int $quantity): void
    {
        $this->items[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'quantity' => $quantity,
        ];
    }
}

class OrderRepository
{
    private array $orders = [];

    public function save(Order $order): void
    {
        // BUG #1: saves the order twice (race condition simulation)
        $this->orders[] = $order;
        $this->orders[] = $order;
    }

    public function all(): array
    {
        return $this->orders;
    }

    public function count(): int
    {
        return count($this->orders);
    }
}

class UserController
{
    private string $currentRole;
    private int $currentUserId;

    public function __construct(string $role, int $userId)
    {
        $this->currentRole = $role;
        $this->currentUserId = $userId;
    }

    /**
     * Delete a user account.
     * Returns ['success' => true] or ['error' => string].
     */
    public function deleteUser(int $targetUserId): array
    {
        // BUG #2: admin can delete their own account — should be prevented
        if ($this->currentRole === 'admin') {
            return ['success' => true, 'message' => 'User deleted'];
        }

        if ($this->currentUserId === $targetUserId) {
            return ['error' => 'Cannot delete your own account'];
        }

        return ['success' => true, 'message' => 'User deleted'];
    }
}

function checkout(Product $product, int $quantity): array
{
    $order = new Order();
    $order->addItem($product, $quantity);
    $product->reduceStock($quantity);

    $repo = new OrderRepository();
    $repo->save($order);

    return ['order_id' => $order->id, 'items' => count($repo->all())];
}
PHP,
                'tests_code' => <<<'PHP'
<?php

use PHPUnit\Framework\TestCase;

class LaravelApiBugsTest extends TestCase
{
    public function test_bug1_order_not_duplicated(): void
    {
        $product = new Product(1, 'Widget', 10);
        $result = checkout($product, 1);
        $this->assertSame(1, $result['items']);
    }

    public function test_bug2_admin_cannot_delete_own_account(): void
    {
        $controller = new UserController('admin', 1);
        $result = $controller->deleteUser(1);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('own account', $result['error']);
    }

    public function test_bug2_non_admin_cannot_delete_own_account(): void
    {
        $controller = new UserController('user', 5);
        $result = $controller->deleteUser(5);
        $this->assertArrayHasKey('error', $result);
    }

    public function test_bug2_admin_can_delete_other_users(): void
    {
        $controller = new UserController('admin', 1);
        $result = $controller->deleteUser(2);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
    }

    public function test_bug3_out_of_stock_returns_false(): void
    {
        $product = new Product(1, 'Widget', 0);
        $this->assertFalse($product->isInStock());
    }

    public function test_bug3_in_stock_returns_true(): void
    {
        $product = new Product(1, 'Widget', 5);
        $this->assertTrue($product->isInStock());
    }
}
PHP,
                'is_premium' => false,
                'price_eur' => null,
            ],

            [
                'title' => 'Resolve the Incident Using Only Logs',
                'slug' => 'resolve-incident-from-logs',
                'difficulty' => ChallengeDifficulty::Intermediate,
                'description' => <<<'MD'
## Resolve the Incident Using Only Logs

A production service is crashing. You have no access to the code — only log lines. Analyze the log entries and identify the root cause.

**Goal:** Implement `analyzeLogs()` that parses a series of log entries and returns a structured incident report.
MD,
                'boilerplate_code' => <<<'PHP'
<?php

/**
 * Analyzes log lines and returns an incident report.
 *
 * Log format: "[TIMESTAMP] [LEVEL] [ENDPOINT] message"
 * Example: "[2026-05-06 12:00:01] [ERROR] [/api/users] Database connection timeout"
 *
 * @param  list<string> $logLines
 * @return array{severity: string, root_cause: string, affected_endpoints: list<string>, error_count: int}
 */
function analyzeLogs(array $logLines): array
{
    // TODO: parse log lines, identify patterns, return incident report
}
PHP,
                'tests_code' => <<<'PHP'
<?php

use PHPUnit\Framework\TestCase;

class LogAnalysisTest extends TestCase
{
    public function test_identifies_database_connection_errors(): void
    {
        $logs = [
            '[2026-05-06 12:00:01] [ERROR] [/api/users] Database connection timeout',
            '[2026-05-06 12:00:02] [ERROR] [/api/orders] Database connection timeout',
            '[2026-05-06 12:00:03] [ERROR] [/api/users] Database connection timeout',
            '[2026-05-06 12:00:04] [INFO] [/api/health] Health check passed',
        ];

        $result = analyzeLogs($logs);

        $this->assertSame('database_connection', $result['root_cause']);
        $this->assertSame(3, $result['error_count']);
    }

    public function test_returns_correct_severity_for_critical(): void
    {
        $logs = array_fill(0, 10, '[2026-05-06 12:00:01] [ERROR] [/api/users] Database connection timeout');

        $result = analyzeLogs($logs);

        $this->assertSame('critical', $result['severity']);
    }

    public function test_identifies_affected_endpoints(): void
    {
        $logs = [
            '[2026-05-06 12:00:01] [ERROR] [/api/users] Server error',
            '[2026-05-06 12:00:02] [ERROR] [/api/orders] Server error',
            '[2026-05-06 12:00:03] [INFO] [/api/health] OK',
        ];

        $result = analyzeLogs($logs);

        $this->assertContains('/api/users', $result['affected_endpoints']);
        $this->assertContains('/api/orders', $result['affected_endpoints']);
        $this->assertNotContains('/api/health', $result['affected_endpoints']);
    }

    public function test_empty_logs_returns_empty_report(): void
    {
        $result = analyzeLogs([]);

        $this->assertSame('low', $result['severity']);
        $this->assertSame(0, $result['error_count']);
    }

    public function test_mixed_levels_classifies_correctly(): void
    {
        $logs = [
            '[2026-05-06 12:00:01] [WARNING] [/api/users] Slow query detected',
            '[2026-05-06 12:00:02] [INFO] [/api/health] OK',
        ];

        $result = analyzeLogs($logs);

        $this->assertSame('low', $result['severity']);
    }
}
PHP,
                'is_premium' => false,
                'price_eur' => null,
            ],

            [
                'title' => 'Final Challenge: Debug Session — 3 Bugs, 60 Minutes',
                'slug' => 'final-debug-session-3-bugs',
                'difficulty' => ChallengeDifficulty::Advanced,
                'description' => <<<'MD'
## Final Challenge: Debug Session — 3 Bugs, 60 Minutes

You've been called in at 11pm. Support has reported 3 critical production bugs affecting the checkout. The CEO is awake. You have 60 minutes.

### Bug #1: "Duplicate charges on credit card"

### Bug #2: "Discount code applied but total is unchanged"

### Bug #3: "Order confirmation email sent to wrong user"

**Goal:** Fix all three bugs. Each test verifies one fix.
MD,
                'boilerplate_code' => <<<'PHP'
<?php

class PaymentProcessor
{
    private array $processedIds = [];

    public function processPayment(int $orderId, int $amountCents): array
    {
        // BUG #1: processes payment twice due to missing idempotency check
        $charge = $this->chargeCard($amountCents);
        $this->processedIds[] = $orderId;
        $charge2 = $this->chargeCard($amountCents);

        return [
            'order_id' => $orderId,
            'total_charged' => $charge['amount'] + $charge2['amount'],
        ];
    }

    private function chargeCard(int $amountCents): array
    {
        return ['amount' => $amountCents, 'status' => 'success'];
    }
}

class DiscountCalculator
{
    /**
     * @param array{price: int, discount_percent: int} $item
     */
    public function calculateDiscount(array $item): int
    {
        // BUG #2: uses wrong variable — should multiply by discount_percent, not 0
        $discountPercent = $item['discount_percent'] ?? 0;
        return (int) ($item['price'] * 0 / 100);
    }
}

class OrderNotifier
{
    private static ?string $cachedEmail = null;

    public function sendConfirmation(object $user, int $orderId): array
    {
        // BUG #3: uses cached email instead of current user's email
        $email = self::$cachedEmail ?? $user->email;
        self::$cachedEmail = $email;

        return ['sent_to' => $email, 'order_id' => $orderId];
    }
}
PHP,
                'tests_code' => <<<'PHP'
<?php

use PHPUnit\Framework\TestCase;

class DebugSessionTest extends TestCase
{
    public function test_bug1_payment_processed_once(): void
    {
        $processor = new PaymentProcessor();
        $result = $processor->processPayment(1, 5000);

        $this->assertSame(5000, $result['total_charged']);
    }

    public function test_bug2_discount_calculated_correctly(): void
    {
        $calc = new DiscountCalculator();

        $item = ['price' => 10000, 'discount_percent' => 20];
        $this->assertSame(2000, $calc->calculateDiscount($item));
    }

    public function test_bug2_no_discount_when_percent_zero(): void
    {
        $calc = new DiscountCalculator();

        $item = ['price' => 10000, 'discount_percent' => 0];
        $this->assertSame(0, $calc->calculateDiscount($item));
    }

    public function test_bug3_email_sent_to_correct_user(): void
    {
        $notifier = new OrderNotifier();

        $user1 = new class { public string $email = 'alice@example.com'; };
        $user2 = new class { public string $email = 'bob@example.com'; };

        $result1 = $notifier->sendConfirmation($user1, 1);
        $result2 = $notifier->sendConfirmation($user2, 2);

        $this->assertSame('alice@example.com', $result1['sent_to']);
        $this->assertSame('bob@example.com', $result2['sent_to']);
    }
}
PHP,
                'is_premium' => false,
                'price_eur' => null,
            ],

            [
                'title' => 'Your App is Slow — Find the Bottleneck in 30 Minutes',
                'slug' => 'find-api-bottleneck',
                'difficulty' => ChallengeDifficulty::Intermediate,
                'description' => <<<'MD'
## Your App is Slow — Find the Bottleneck in 30 Minutes

Users report that the product listing page takes 8+ seconds to load. Profile the code and identify the N+1 query problem, then fix it.

**Goal:** Rewrite `getProductsWithCategories()` to batch the category lookups instead of querying per product.
MD,
                'boilerplate_code' => <<<'PHP'
<?php

class Category
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}

class Product
{
    public function __construct(
        public int $id,
        public string $name,
        public int $categoryId,
        public ?Category $category = null,
    ) {}
}

function getCategoryById(int $id): Category
{
    static $categories = [
        1 => new Category(1, 'Electronics'),
        2 => new Category(2, 'Books'),
        3 => new Category(3, 'Clothing'),
    ];
    return $categories[$id] ?? new Category($id, 'Unknown');
}

function getAllProducts(): array
{
    return [
        new Product(1, 'Laptop', 1),
        new Product(2, 'Phone', 1),
        new Product(3, 'PHP Book', 2),
        new Product(4, 'T-Shirt', 3),
    ];
}

function getProductsWithCategories(): array
{
    $products = getAllProducts();
    $result = [];

    foreach ($products as $product) {
        // BUG: N+1 query — fetches category for each product individually
        $category = getCategoryById($product->categoryId);
        $result[] = [
            'id' => $product->id,
            'name' => $product->name,
            'category' => $category->name,
        ];
    }

    return $result;
}

function getQueryCount(): int
{
    static $count = 0;
    return $count;
}

function resetQueryCount(): void {}
PHP,
                'tests_code' => <<<'PHP'
<?php

use PHPUnit\Framework\TestCase;

class BottleneckTest extends TestCase
{
    public function test_returns_correct_products_with_categories(): void
    {
        $result = getProductsWithCategories();

        $this->assertCount(4, $result);
        $this->assertSame('Electronics', $result[0]['category']);
        $this->assertSame('Books', $result[2]['category']);
        $this->assertSame('Clothing', $result[3]['category']);
    }

    public function test_batched_lookups_reduces_queries(): void
    {
        resetQueryCount();
        getProductsWithCategories();

        $this->assertLessThanOrEqual(1, getQueryCount());
    }

    public function test_product_names_are_correct(): void
    {
        $result = getProductsWithCategories();

        $names = array_map(fn($p) => $p['name'], $result);
        $this->assertContains('Laptop', $names);
        $this->assertContains('PHP Book', $names);
    }
}
PHP,
                'is_premium' => false,
                'price_eur' => null,
            ],

            [
                'title' => 'Instrument This API End-to-End',
                'slug' => 'instrument-api-end-to-end',
                'difficulty' => ChallengeDifficulty::Advanced,
                'description' => <<<'MD'
## Instrument This API End-to-End

Build a lightweight instrumentation layer that tracks request duration, status codes, and error rates.

**Goal:** Implement the `MetricsCollector` class that records requests and provides a structured metrics report.
MD,
                'boilerplate_code' => <<<'PHP'
<?php

class MetricsCollector
{
    private int $totalRequests = 0;
    private int $totalErrors = 0;
    private float $totalDuration = 0;

    public function startRequest(): void
    {
        // TODO: record request start time
    }

    public function endRequest(int $statusCode): void
    {
        // TODO: calculate duration, increment counters
    }

    /**
     * @return array{total_requests: int, total_errors: int, error_rate: float, avg_duration_ms: float}
     */
    public function snapshot(): array
    {
        return [
            'total_requests' => 0,
            'total_errors' => 0,
            'error_rate' => 0.0,
            'avg_duration_ms' => 0.0,
        ];
    }
}
PHP,
                'tests_code' => <<<'PHP'
<?php

use PHPUnit\Framework\TestCase;

class MetricsCollectorTest extends TestCase
{
    public function test_records_successful_requests(): void
    {
        $collector = new MetricsCollector();

        $collector->startRequest();
        usleep(1000);
        $collector->endRequest(200);

        $snapshot = $collector->snapshot();

        $this->assertSame(1, $snapshot['total_requests']);
        $this->assertSame(0, $snapshot['total_errors']);
    }

    public function test_records_error_requests(): void
    {
        $collector = new MetricsCollector();

        $collector->startRequest();
        $collector->endRequest(500);

        $snapshot = $collector->snapshot();

        $this->assertSame(1, $snapshot['total_errors']);
        $this->assertSame(100.0, $snapshot['error_rate']);
    }

    public function test_calculates_correct_error_rate(): void
    {
        $collector = new MetricsCollector();

        $collector->startRequest(); $collector->endRequest(200);
        $collector->startRequest(); $collector->endRequest(500);
        $collector->startRequest(); $collector->endRequest(200);
        $collector->startRequest(); $collector->endRequest(404);

        $snapshot = $collector->snapshot();

        $this->assertSame(4, $snapshot['total_requests']);
        $this->assertSame(2, $snapshot['total_errors']);
        $this->assertSame(50.0, $snapshot['error_rate']);
    }

    public function test_snapshot_returns_avg_duration(): void
    {
        $collector = new MetricsCollector();

        $collector->startRequest();
        $collector->endRequest(200);

        $snapshot = $collector->snapshot();

        $this->assertIsFloat($snapshot['avg_duration_ms']);
        $this->assertGreaterThan(0, $snapshot['avg_duration_ms']);
    }

    public function test_empty_snapshot_has_zeroes(): void
    {
        $collector = new MetricsCollector();
        $snapshot = $collector->snapshot();

        $this->assertSame(0, $snapshot['total_requests']);
        $this->assertSame(0, $snapshot['total_errors']);
        $this->assertSame(0.0, $snapshot['error_rate']);
    }
}
PHP,
                'is_premium' => false,
                'price_eur' => null,
            ],

            [
                'title' => 'Trace a Bug from Frontend to Database',
                'slug' => 'trace-frontend-to-database',
                'difficulty' => ChallengeDifficulty::Advanced,
                'description' => <<<'MD'
## Trace a Bug from Frontend to Database

A user reports that updating their profile fails silently. The frontend shows success, but the data is never saved. Trace the bug through the request lifecycle and fix it.

**Goal:** Identify and fix the bugs in the request validation, service layer, and database query.
MD,
                'boilerplate_code' => <<<'PHP'
<?php

class Request
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }
}

class Validator
{
    private array $rules;
    private array $errors = [];

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function validate(Request $request): array
    {
        // BUG #1: always returns empty errors even when validation fails
        return $this->errors;
    }

    public function fail(string $field, string $message): void
    {
        $this->errors[$field] = $message;
    }
}

class ProfileService
{
    private array $database = [];

    public function updateProfile(int $userId, array $data): array
    {
        // BUG #2: uses string concatenation for ID instead of integer cast
        $key = "user_" . $userId;

        // BUG #3: overwrites all fields instead of merging with existing data
        $this->database[$key] = $data;

        return $this->database[$key];
    }

    public function getProfile(int $userId): ?array
    {
        return $this->database["user_{$userId}"] ?? null;
    }
}

function handleProfileUpdate(Request $request, int $userId): array
{
    $validator = new Validator([
        'name' => 'required',
        'email' => 'required|email',
    ]);

    $errors = $validator->validate($request);

    if (!empty($errors)) {
        return ['error' => 'Validation failed', 'errors' => $errors];
    }

    $service = new ProfileService();
    $profile = $service->updateProfile($userId, [
        'name' => $request->get('name'),
        'email' => $request->get('email'),
    ]);

    return ['success' => true, 'profile' => $profile];
}
PHP,
                'tests_code' => <<<'PHP'
<?php

use PHPUnit\Framework\TestCase;

class TraceBugTest extends TestCase
{
    public function test_validation_catches_missing_name(): void
    {
        $request = new Request(['email' => 'test@example.com']);
        $result = handleProfileUpdate($request, 1);

        $this->assertArrayHasKey('error', $result);
        $this->assertSame('Validation failed', $result['error']);
    }

    public function test_validation_catches_missing_email(): void
    {
        $request = new Request(['name' => 'John']);
        $result = handleProfileUpdate($request, 1);

        $this->assertArrayHasKey('error', $result);
    }

    public function test_valid_request_returns_success(): void
    {
        $request = new Request([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
        $result = handleProfileUpdate($request, 1);

        $this->assertTrue($result['success']);
        $this->assertSame('John Doe', $result['profile']['name']);
    }

    public function test_update_preserves_existing_fields(): void
    {
        $service = new ProfileService();

        $service->updateProfile(1, [
            'name' => 'John',
            'email' => 'john@example.com',
            'bio' => 'Developer',
        ]);

        $service->updateProfile(1, [
            'name' => 'John Updated',
            'email' => 'john@example.com',
        ]);

        $profile = $service->getProfile(1);
        $this->assertSame('John Updated', $profile['name']);
        $this->assertSame('john@example.com', $profile['email']);
    }
}
PHP,
                'is_premium' => false,
                'price_eur' => null,
            ],

            [
                'title' => 'Final Challenge: Simulate a Complete Incident Response',
                'slug' => 'simulate-incident-response',
                'difficulty' => ChallengeDifficulty::Expert,
                'description' => <<<'MD'
## Final Challenge: Simulate a Complete Incident Response

A production database migration went wrong. Half the users have null emails, orders are orphaned, and the API is returning inconsistent data.

**Goal:** Implement an incident response system that detects anomalies, generates a recovery plan, executes rollback commands, and verifies data integrity.
MD,
                'boilerplate_code' => <<<'PHP'
<?php

class IncidentResponse
{
    private array $anomalies = [];
    private array $recoverySteps = [];

    /**
     * Analyze user records for anomalies.
     * @param list<array{id: int, email: ?string, role: string}> $users
     */
    public function detectAnomalies(array $users): void
    {
        // TODO: find users with null emails, invalid roles, duplicate entries
    }

    /**
     * Generate recovery steps in correct order.
     * @return list<string>
     */
    public function generateRecoveryPlan(): array
    {
        return [];
    }

    /**
     * Execute the recovery and return verification results.
     * @return array{success: bool, fixed: int, remaining_issues: int}
     */
    public function executeRecovery(array &$users): array
    {
        return ['success' => false, 'fixed' => 0, 'remaining_issues' => count($users)];
    }

    /**
     * Verify data integrity after recovery.
     * @param list<array{id: int, email: ?string, role: string}> $users
     */
    public function verifyIntegrity(array $users): array
    {
        return ['valid' => 0, 'invalid' => 0, 'issues' => []];
    }
}
PHP,
                'tests_code' => <<<'PHP'
<?php

use PHPUnit\Framework\TestCase;

class IncidentResponseTest extends TestCase
{
    public function test_detects_null_emails(): void
    {
        $users = [
            ['id' => 1, 'email' => 'alice@example.com', 'role' => 'admin'],
            ['id' => 2, 'email' => null, 'role' => 'user'],
            ['id' => 3, 'email' => 'charlie@example.com', 'role' => 'user'],
        ];

        $ir = new IncidentResponse();
        $ir->detectAnomalies($users);
        $plan = $ir->generateRecoveryPlan();

        $this->assertNotEmpty($plan);
    }

    public function test_detects_invalid_roles(): void
    {
        $users = [
            ['id' => 1, 'email' => 'alice@example.com', 'role' => 'admin'],
            ['id' => 2, 'email' => 'bob@example.com', 'role' => 'superadmin'],
        ];

        $ir = new IncidentResponse();
        $ir->detectAnomalies($users);

        $result = $ir->verifyIntegrity($users);
        $this->assertGreaterThan(0, $result['invalid']);
    }

    public function test_execute_recovery_fixes_issues(): void
    {
        $users = [
            ['id' => 1, 'email' => null, 'role' => 'user'],
            ['id' => 2, 'email' => 'bob@example.com', 'role' => 'admin'],
        ];

        $ir = new IncidentResponse();
        $ir->detectAnomalies($users);
        $result = $ir->executeRecovery($users);

        $this->assertGreaterThan(0, $result['fixed']);
    }

    public function test_verify_integrity_returns_valid_count(): void
    {
        $users = [
            ['id' => 1, 'email' => 'valid@example.com', 'role' => 'admin'],
            ['id' => 2, 'email' => 'valid2@example.com', 'role' => 'user'],
        ];

        $ir = new IncidentResponse();
        $result = $ir->verifyIntegrity($users);

        $this->assertSame(2, $result['valid']);
        $this->assertSame(0, $result['invalid']);
    }

    public function test_clean_data_has_no_anomalies(): void
    {
        $users = [
            ['id' => 1, 'email' => 'a@example.com', 'role' => 'admin'],
            ['id' => 2, 'email' => 'b@example.com', 'role' => 'user'],
        ];

        $ir = new IncidentResponse();
        $ir->detectAnomalies($users);
        $plan = $ir->generateRecoveryPlan();

        $this->assertEmpty($plan);
    }
}
PHP,
                'is_premium' => false,
                'price_eur' => null,
            ],

            [
                'title' => 'Optimize This API Endpoint',
                'slug' => 'optimize-api-endpoint',
                'difficulty' => ChallengeDifficulty::Advanced,
                'description' => <<<'MD'
## Optimize This API Endpoint

An API endpoint loads all products, then filters and sorts them in PHP. With 100k+ products, this is catastrophic.

**Goal:** Implement efficient filtering and sorting that simulates database-level optimization.
MD,
                'boilerplate_code' => <<<'PHP'
<?php

/**
 * @param list<array{id: int, name: string, price: int, category: string, active: bool}> $products
 * @return list<array{id: int, name: string, price: int, category: string}>
 */
function getFilteredProducts(array $products, array $filters): array
{
    // BUG: loads everything, doesn't filter by category, doesn't sort by price
    $result = [];
    foreach ($products as $p) {
        $result[] = [
            'id' => $p['id'],
            'name' => $p['name'],
            'price' => $p['price'],
            'category' => $p['category'],
        ];
    }
    return $result;
}
PHP,
                'tests_code' => <<<'PHP'
<?php

use PHPUnit\Framework\TestCase;

class OptimizeEndpointTest extends TestCase
{
    private array $catalog;

    protected function setUp(): void
    {
        $this->catalog = [
            ['id' => 1, 'name' => 'Laptop', 'price' => 120000, 'category' => 'electronics', 'active' => true],
            ['id' => 2, 'name' => 'Phone', 'price' => 80000, 'category' => 'electronics', 'active' => true],
            ['id' => 3, 'name' => 'Shirt', 'price' => 3000, 'category' => 'clothing', 'active' => true],
            ['id' => 4, 'name' => 'Book', 'price' => 1500, 'category' => 'books', 'active' => false],
            ['id' => 5, 'name' => 'Tablet', 'price' => 50000, 'category' => 'electronics', 'active' => true],
        ];
    }

    public function test_filters_by_category(): void
    {
        $result = getFilteredProducts($this->catalog, ['category' => 'electronics']);

        $this->assertCount(3, $result);
        foreach ($result as $item) {
            $this->assertSame('electronics', $item['category']);
        }
    }

    public function test_filters_out_inactive_products(): void
    {
        $result = getFilteredProducts($this->catalog, []);

        foreach ($result as $item) {
            $this->assertNotSame(4, $item['id']);
        }
    }

    public function test_sorts_by_price_ascending(): void
    {
        $result = getFilteredProducts($this->catalog, ['sort' => 'price_asc']);

        $this->assertLessThanOrEqual($result[1]['price'], $result[2]['price']);
    }

    public function test_returns_all_active_when_no_filters(): void
    {
        $result = getFilteredProducts($this->catalog, []);

        $this->assertCount(4, $result);
    }

    public function test_empty_catalog_returns_empty(): void
    {
        $result = getFilteredProducts([], []);
        $this->assertSame([], $result);
    }
}
PHP,
                'is_premium' => false,
                'price_eur' => null,
            ],

            [
                'title' => 'Challenge: Untangle This Git History',
                'slug' => 'untangle-git-history',
                'difficulty' => ChallengeDifficulty::Intermediate,
                'description' => <<<'MD'
## Untangle This Git History

A merge conflict has corrupted the deployment. Two developers edited the same config file and force-pushed. You need to reconstruct the correct state from the commit messages.

**Goal:** Implement a `mergeConfigs()` function that takes two conflicting config arrays and a resolution strategy.

Strategies: `"ours"`, `"theirs"`, `"combine"`
MD,
                'boilerplate_code' => <<<'PHP'
<?php

/**
 * Merges two config arrays using the specified strategy.
 *
 * @param array<string, mixed> $ours
 * @param array<string, mixed> $theirs
 */
function mergeConfigs(array $ours, array $theirs, string $strategy): array
{
    // TODO: implement merge strategies
    return [];
}
PHP,
                'tests_code' => <<<'PHP'
<?php

use PHPUnit\Framework\TestCase;

class GitMergeTest extends TestCase
{
    private array $ours = [
        'db_host' => 'localhost',
        'db_port' => 3306,
        'cache_ttl' => 3600,
        'debug' => false,
    ];

    private array $theirs = [
        'db_host' => 'prod-db.internal',
        'db_port' => 5432,
        'api_key' => 'secret123',
        'debug' => true,
    ];

    public function test_ours_strategy_keeps_our_values(): void
    {
        $result = mergeConfigs($this->ours, $this->theirs, 'ours');

        $this->assertSame('localhost', $result['db_host']);
        $this->assertSame(3306, $result['db_port']);
    }

    public function test_ours_strategy_fills_gaps(): void
    {
        $result = mergeConfigs($this->ours, $this->theirs, 'ours');

        $this->assertSame('secret123', $result['api_key']);
    }

    public function test_theirs_strategy_keeps_their_values(): void
    {
        $result = mergeConfigs($this->ours, $this->theirs, 'theirs');

        $this->assertSame('prod-db.internal', $result['db_host']);
        $this->assertSame(5432, $result['db_port']);
    }

    public function test_combine_uses_ours_for_conflicts(): void
    {
        $result = mergeConfigs($this->ours, $this->theirs, 'combine');

        $this->assertSame('localhost', $result['db_host']);
        $this->assertSame('secret123', $result['api_key']);
        $this->assertSame(3600, $result['cache_ttl']);
    }

    public function test_empty_arrays(): void
    {
        $this->assertSame([], mergeConfigs([], [], 'ours'));
        $this->assertSame(['a' => 1], mergeConfigs([], ['a' => 1], 'theirs'));
    }
}
PHP,
                'is_premium' => false,
                'price_eur' => null,
            ],
        ];
    }
}
