## The situation

Your company launched in Ireland charging through Stripe. Then the Brazilian
market opened, where PayPal converts better — and the product manager's next
sentence was inevitable: *"can we choose the gateway per country at runtime?"*

If `CheckoutService` had `new StripeGateway()` hardcoded inside it, this
request means surgery. Because the checkout code depends on a **contract**
instead of a vendor, it means writing one new class. That's the **Strategy
pattern**: the algorithm (how to charge) is swappable behind an interface,
and the caller (`PaymentProcessor`) neither knows nor cares which one it got.

## Your task

Implement the three missing methods:

- `StripeGateway::charge()` — succeeds, `transactionId` starts with `"stripe_"`.
- `PayPalGateway::charge()` — succeeds, `transactionId` starts with `"paypal_"`.
- `PaymentProcessor::process()` — validates first: `$amountCents <= 0` returns
  a **failed** `PaymentResult` with `error = "Amount must be positive"` and no
  gateway call; otherwise it delegates to the injected gateway.

The hard rule: `PaymentProcessor` must never mention `StripeGateway` or
`PayPalGateway` — only the `PaymentGateway` interface.

## Examples

| **Setup** | **Result** |
| `new PaymentProcessor(new StripeGateway())`, 500 cents | success, id `stripe_…` |
| `new PaymentProcessor(new PayPalGateway())`, 500 cents | success, id `paypal_…` |
| any gateway, 0 or −100 cents | `success = false`, positive-amount error |

## Hints

- **Hint 1:** `PaymentResult` is a readonly value object — you create a new one per outcome (`new PaymentResult(true, 'stripe_' . uniqid())`), never mutate one.
- **Hint 2:** the guard clause in `process()` runs *before* the delegation — validating the amount is the processor's job precisely because it must hold for every gateway.
- **Hint 3:** for the failed result, the transaction id can be an empty string — there was no transaction. The tests assert `success` and `error`, not a fake id.

## In the real world

This is dependency inversion doing real work. Laravel's service container
makes the wiring declarative — bind `PaymentGateway` to a concrete class per
config, inject the interface everywhere. Tests exploit the same seam: a
`FakeGateway` that always succeeds (or always fails) makes checkout testable
without touching Stripe — which is exactly how this project's own test suite
fakes Stripe, Judge0 and Gemini behind `Http::fake()`. When you catch
yourself writing `if ($provider === 'stripe') … elseif …` inside business
logic, the refactor you're reaching for has a name, and you've now built it.
