## Strategy Pattern: Payment Gateway

The **Strategy pattern** separates an algorithm from the code that uses it. The caller does not know which gateway it is talking to — it only knows the contract (`PaymentGateway`). This makes it trivial to add new gateways, mock them in tests, or swap them at runtime based on configuration.

**Goal:** implement the three missing methods:
- `StripeGateway::charge()` — always succeeds, `transactionId` starts with `"stripe_"`
- `PayPalGateway::charge()` — always succeeds, `transactionId` starts with `"paypal_"`
- `PaymentProcessor::process()` — delegates to the injected gateway; if `$amountCents <= 0`, return a failed `PaymentResult` with `error = "Amount must be positive"`

The `PaymentProcessor` must never reference `StripeGateway` or `PayPalGateway` directly — only the interface.
