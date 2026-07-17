## Enum-Driven State Machine

Combine PHP 8.1 **enums** with **readonly properties** to build a lightweight state machine. Valid transitions are encoded directly on the enum, making illegal transitions impossible to overlook.

**Goal:** implement `transitions()` on `InvoiceStatus` returning the allowed next states, and `transitionTo()` on `Invoice` that:
- returns a new `Invoice` with the updated status if the transition is valid,
- returns `null` if the transition is forbidden.

Valid transitions:
- `Draft` → `Sent`
- `Sent` → `Paid` or `Cancelled`
- `Paid` and `Cancelled` are terminal (no transitions)
