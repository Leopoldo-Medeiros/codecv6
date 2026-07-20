## The situation

23:00. Three customer-facing bugs hit checkout at once, support is drowning,
and the CEO is awake and typing in Slack. You have the code, a failing test
suite, and 60 minutes. This is the capstone of the debug katas: each bug on
its own is one you've seen before — under time pressure, the skill is
**triage**: reproduce, isolate, fix, verify, next.

The three reports:

- **Bug #1 — "Duplicate charges on credit card."** Every order charges the
  card twice. Finance says total charged is exactly 2× the order amount.
- **Bug #2 — "Discount code applied but total unchanged."** A 20% code on a
  €100.00 item should discount €20.00; customers get €0.00 every time.
- **Bug #3 — "Confirmation email sent to the wrong user."** Bob completed an
  order and the confirmation went to Alice — the *previous* customer.

## Your task

Fix all three, one class each: `PaymentProcessor::processPayment()`,
`DiscountCalculator::calculateDiscount()`, `OrderNotifier::sendConfirmation()`.
Each test verifies one fix:

| **Bug** | **Passing looks like** |
| #1 | order of 5000 cents charges exactly `5000` |
| #2 | `price 10000, percent 20` → `2000`; `percent 0` → `0` |
| #3 | Alice's mail to Alice, then Bob's mail to Bob |

## Hints

- **Hint 1 (#1):** the method charges, records the order id as processed… and charges again. One of those calls has no reason to exist — and the `$processedIds` bookkeeping hints at what a real idempotency guard would check *before* charging.
- **Hint 2 (#2):** the discount percent is carefully read into a variable — then the formula multiplies by a literal `0` instead of using it. Classic half-finished refactor.
- **Hint 3 (#3):** a `static` cached email means the *first* customer of the process wins forever — the same static-state trap as the request-ID kata, now with real blast radius. Ask whether caching belongs here at all.

## In the real world

All three are production classics. Double-charging is why payment APIs have
**idempotency keys** — this project's own Stripe webhook claims each event id
in a `processed_stripe_events` table before acting, which is Bug #1's fix at
architectural scale. The dead-variable discount is the signature of an
interrupted refactor — the reviewer's lesson is *grep for variables that are
assigned but never used*. And the static email cache is cross-request state
leakage, the bug family that turns "works in dev" into "sends customer data
to the wrong person" under Octane or a queue worker. Sixty minutes is
generous once you trust the tests: reproduce → isolate → fix → green → next.
