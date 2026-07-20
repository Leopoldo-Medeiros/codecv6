## The situation

Last month someone marked a **cancelled** invoice as **paid** with a raw
`UPDATE`, and finance spent a day unwinding the ledger. The postmortem action
item: *"illegal status transitions must be unrepresentable in code, not
discouraged by convention."*

Scattered `if ($invoice->status === 'draft')` checks can't deliver that —
every new call site is a chance to forget one. A **state machine** can: put
the allowed transitions *on the status itself*, and route every change
through one method that consults them. PHP 8.1 enums plus readonly
properties make the whole thing about twenty lines.

## Your task

The lifecycle to encode:

- `Draft` → `Sent`
- `Sent` → `Paid` or `Cancelled`
- `Paid` and `Cancelled` are **terminal** — nothing leaves them.

Implement two methods:

- `InvoiceStatus::transitions(): array` — the allowed next states for each
  case (terminal cases return `[]`).
- `Invoice::transitionTo(InvoiceStatus $next): ?static` — returns a **new**
  `Invoice` (same id, new status) when the transition is legal, `null` when
  it isn't. The original invoice is never mutated — its properties are
  `readonly`.

## Examples

| **From** | **To** | **Result** |
| `Draft` | `Sent` | new invoice, status `Sent`, same id |
| `Draft` | `Paid` | `null` — can't pay an unsent invoice |
| `Sent` | `Paid` or `Cancelled` | allowed |
| `Paid` | `Cancelled` | `null` — terminal |

## Hints

- **Hint 1:** `transitions()` is a single `match ($this)` returning arrays of cases. The whole business rule ends up readable as a table — that's the point.
- **Hint 2:** for the legality check, `in_array($next, $this->status->transitions(), true)` works — enum cases are singletons, so strict comparison compares identity.
- **Hint 3:** "returns a new Invoice" is forced by `readonly` — `return new static($this->id, $next);`. Immutability isn't a style choice here; it's what makes a half-applied transition impossible.

## In the real world

Order lifecycles, subscription states, deployment pipelines, document
approval — every non-trivial domain has a state machine, acknowledged or
not. The unacknowledged ones live as scattered status `if`s and eventually
produce the cancelled-invoice-marked-paid incident. This codebase has a live
example: a `Payment` is only ever marked paid through `markSessionPaid`,
which refuses to overwrite a settled payment — a two-state machine guarding
real money. When requirements say "an X can only go from A to B", write it
as data on an enum, not as vigilance.
