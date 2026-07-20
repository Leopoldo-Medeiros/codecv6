## The situation

Your payments code passes money around as floats, and finance has begun
noticing cents disappearing: `0.1 + 0.2 !== 0.3` in floating point, and a
refund of `19.99 * 3` rounds differently in two services. Meanwhile a bug
last sprint *mutated* an order's total inside a logging helper — nobody
noticed for a week because any code holding the object could change it.

Both bug families die with one pattern: a **Money value object** — integer
cents (no float drift in arithmetic), immutable (`readonly` — nobody can
change an amount, they can only derive a new one), currency-aware (no more
adding euros to reais by accident).

## Your task

Implement three methods on `Money` (constructor holds `int $amount` in cents
and `string $currency`, both `readonly`):

- `add(Money $other): Money` — returns a **new** `Money` with the summed
  amount; throws `\InvalidArgumentException` when currencies differ.
- `toFloat(): float` — cents to major units (`1250` → `12.5`). Floats are
  for *display and export only* — arithmetic stays in integer cents.
- `format(): string` — `'€10.00'` for EUR, `'R$99.90'` for BRL, always two
  decimals.

## Examples

| **Expression** | **Result** |
| `(new Money(1000, 'EUR'))->add(new Money(500, 'EUR'))` | new `Money(1500, 'EUR')`; originals untouched |
| `(new Money(1000, 'EUR'))->add(new Money(500, 'BRL'))` | throws `InvalidArgumentException` |
| `(new Money(1250, 'EUR'))->toFloat()` | `12.5` |
| `(new Money(9990, 'BRL'))->format()` | `'R$99.90'` |

## Hints

- **Hint 1:** `add()` can't mutate even if you try — assigning to `$this->amount` on a readonly property throws. The only possible implementation returns `new self(...)`, which is the lesson.
- **Hint 2:** guard clause first: compare currencies, throw with a clear message, *then* construct the sum.
- **Hint 3:** for `format()`, `number_format($this->amount / 100, 2, '.', '')` gives the two-decimal string; a small `match` on the currency picks the prefix.

## In the real world

Integer minor units is how the industry stores money — Stripe's entire API
speaks cents, and this project's `config/pricing.php` stores every tier in
minor units for the same reason. The immutability half is just as
load-bearing: a value object that can't change can be shared, cached and
logged without defensive copying (compare the shopping-cart clone kata,
where a *mutable* shared object caused exactly that corruption). When money
math gets serious — allocation without losing cents, currency conversion —
reach for `moneyphp/money`; what you built here is its core design, and now
you know *why* it's designed that way.
