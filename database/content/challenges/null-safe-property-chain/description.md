## The situation

Sentry is paging: `Attempt to read property "name" on null` in the profile
header. The template innocently renders the user's city — but a user who
skipped onboarding has no address, and one who typed only a street has no
city. Production data always has more holes than your fixtures.

Before PHP 8.0 the defensive version was a pyramid of `isset()` checks or
three nested `if`s. The **null-safe operator** `?->` collapses all of it into
one expression that short-circuits to `null` at the first missing link.

## Your task

Implement `getCity(?User $user): ?string` — return the city name from the
`user → address → city → name` chain, or `null` if **any** link is absent.
One expression with `?->` is all it takes.

## Examples

| **Graph** | **Result** |
| `User(Address(City('Dublin')))` | `'Dublin'` |
| `null` (no user) | `null` |
| `User(null)` — no address | `null` |
| `User(Address(null))` — no city | `null` |

## Hints

- **Hint 1:** the chain from the description is nearly the whole answer: `$user?->address?->city?->name`. Understand *why* each `?->` is needed — which test would fail if you made just one of them a plain `->`?
- **Hint 2:** the last hop (`->name`) can stay a normal arrow: `City::$name` is typed non-nullable `string`, so once you have a `City`, `name` exists. Null-safety belongs where `null` is actually possible.

## In the real world

This is everyday Laravel: `$order->customer?->address?->city` when the
relation may not be loaded or may not exist, `auth()->user()?->profile`, or
`$payment?->refunded_at?->format('Y-m-d')`. Two habits to take with you:
don't spray `?->` on every arrow (it silences real bugs — if `address` should
*never* be null, you want the exception), and remember `?->` returns `null`
rather than throwing, so the *caller* must be honest about its own nullable
return type — exactly like this function's `?string`.
