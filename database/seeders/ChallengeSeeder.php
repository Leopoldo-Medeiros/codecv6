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

        $challenges = [
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
        ];

        foreach ($challenges as $data) {
            Challenge::create([...$data, 'created_by' => $admin->id]);
        }
    }
}
