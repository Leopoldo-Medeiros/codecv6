## The situation

The orders dashboard shows a status chip per order — colored badge, readable
label. Right now that display logic lives in the Blade template as a chain of
`@if($status === 'pending')` checks, and it's already gone wrong twice: a new
status got a label but no color, and mobile shipped a different label than
web for the same status.

The reviewer's fix request: *"Put the display logic ON the status. One
authoritative place, and the compiler tells us when a case is missed."*
PHP 8.1 backed enums are built for exactly this — cases that carry behavior.

## Your task

Complete the `OrderStatus` enum so each case answers for itself:

| **Case** | **`label()`** | **`color()`** |
| `Pending` | `Pending payment` | `yellow` |
| `Paid` | `Paid` | `green` |
| `Cancelled` | `Cancelled` | `red` |

The enum is string-backed (`'pending'`, `'paid'`, `'cancelled'`), so
`OrderStatus::from('paid')` must keep working — that's how the value stored
in the database becomes a case again.

## Hints

- **Hint 1:** inside an enum method, `$this` is the current case — `match ($this) { OrderStatus::Pending => 'Pending payment', … }` reads like a table.
- **Hint 2:** write the `match` **without** a `default` arm. That's a feature: add a `Refunded` case next sprint and PHP throws `UnhandledMatchError` instead of silently rendering an unstyled chip — the compiler-nags-you behavior the reviewer asked for.

## In the real world

This is the recommended Laravel pattern: cast the column
(`'status' => OrderStatus::class`) and the model hands you an enum with
behavior, not a magic string. This codebase practices it —
`ChallengeDifficulty` carries per-level XP logic and `PaymentTier` knows
`isRecurring()` — and its architecture review flags the remaining
string-status columns as debt precisely because they *can't* do what you just
did. When display logic scatters across templates, "put it on the enum" is
the refactor with the highest tidiness-per-line ratio in modern PHP.
