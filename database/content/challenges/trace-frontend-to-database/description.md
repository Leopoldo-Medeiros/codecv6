## The situation

A support ticket, verbatim: *"I updated my profile, the page said 'saved
successfully', but when I came back my old name was still there."*

Silent failures are the worst class of bug — every layer *looks* fine in
isolation. The code in this challenge is a miniature of a real request
lifecycle: a `Request` wrapper, a `Validator`, a `ProfileService` (standing in
for your database layer), and the `handleProfileUpdate()` controller function
that wires them together. Somewhere along that chain, data goes in and truth
doesn't come out.

Your job is the senior debugging move: **don't guess a layer — trace the data
through all of them** until the place where what-goes-in stops matching
what-comes-out.

## Your task

Find and fix the bugs so the whole lifecycle behaves:

- A payload missing `name` or `email` must **fail validation** — the handler
  returns `['error' => 'Validation failed', …]`, and nothing is written.
- A valid payload returns `['success' => true, 'profile' => …]` with the new
  values actually persisted.
- A **second** update must not corrupt what the first one wrote — updating
  name and email must leave previously saved data consistent.

There is more than one bug, and they are not all in the same class.

## Examples

| **Request** | **Expected** |
| `['email' => 'test@example.com']` (no name) | `error: 'Validation failed'` |
| `['name' => 'John']` (no email) | `error` key present |
| `['name' => 'John Doe', 'email' => 'john@…']` | `success: true`, profile shows `John Doe` |
| update twice, second without `bio` | second update's name/email win |

## Hints

- **Hint 1:** start at the outermost layer and write down what each one *receives* and *returns* for one failing test — the bug is at the first mismatch, not necessarily where the test asserts.
- **Hint 2:** validation bugs love inverted conditions and wrong array functions (`isset` vs `array_key_exists`, `||` vs `&&`). Read the Validator's loop as if you were the data.
- **Hint 3:** in the persistence layer, watch how the update merges into existing state — replacing when it should merge (or vice versa) is exactly the kind of bug that survives a demo and dies in production.

## In the real world

This is the daily shape of full-stack debugging: frontend says 200, database
says nothing changed, and the truth is in a middle layer nobody suspected. The
production-grade version of "trace the data" is distributed tracing — one
trace ID following the request across services — which is precisely what the
Observability 101 path teaches with real telemetry. This challenge is that
skill with the training wheels of a test suite.
