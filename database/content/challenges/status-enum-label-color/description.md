## Status Enum — Label & Color

PHP 8.1 **backed enums** can carry methods that centralise display logic, keeping `match` expressions out of view templates.

**Goal:** complete the `OrderStatus` enum so that each case returns a human-readable `label()` and a Tailwind CSS `color()` string.

| Case | Label | Color |
|------|-------|-------|
| `Pending` | `"Pending payment"` | `"yellow"` |
| `Paid` | `"Paid"` | `"green"` |
| `Cancelled` | `"Cancelled"` | `"red"` |
