## The situation

Yesterday the checkout API threw its first 500 in weeks. The stack trace ends
deep inside `OrderService` — a `TypeError` because `quantity` arrived as the
string `"lots"`. A mobile client shipped a bug, sent garbage, and your business
logic tried to do math with it.

Your tech lead's review comment is blunt: *"Bad input should never reach
business logic. Validate at the boundary, return a 422 with real error
messages, and OrderService gets to assume clean data forever."*

You're implementing that boundary.

## Your task

Implement `validateOrder(array $input): array`. It returns an **array of error
strings** — one message per failed rule. An empty array means the payload is
valid. The rules:

- `product_id` — required, integer, greater than 0
- `quantity` — required, integer, between 1 and 100 (inclusive)
- `currency` — required, one of `eur`, `brl`

Every failing rule adds its own message, so a payload that's wrong in three
ways comes back with three errors — the client fixes everything in one round
trip instead of playing error whack-a-mole.

## Examples

| **Payload** | **Result** |
| `['product_id' => 5, 'quantity' => 2, 'currency' => 'eur']` | `[]` — valid |
| `['quantity' => 2, 'currency' => 'eur']` | 1 error — missing `product_id` |
| `['product_id' => 5, 'quantity' => 0, 'currency' => 'eur']` | 1 error — quantity below 1 |
| `['product_id' => 5, 'quantity' => 101, 'currency' => 'eur']` | 1 error — quantity above 100 |
| `['product_id' => 5, 'quantity' => 2, 'currency' => 'usd']` | 1 error — unsupported currency |

## Hints

- **Hint 1:** check presence first (`isset` / `array_key_exists`), then type, then range — a missing field shouldn't also trigger a "must be greater than 0" complaint.
- **Hint 2:** `is_int()` is stricter than you think: `"5"` fails it. That strictness is the point of the exercise.
- **Hint 3:** for the currency allow-list, `in_array($input['currency'], ['eur', 'brl'], true)` — the third argument makes the comparison strict.

## In the real world

This is exactly what a Laravel FormRequest does: `'quantity' =>
'required|integer|between:1,100'`, failing with a 422 and a structured error
bag before the controller runs. This project's own `WaitlistRequest` and
`UserRequest` are production examples. Building the validator by hand once
teaches you what those rule strings actually promise — and why "the service
layer can trust its input" is a contract worth defending.
