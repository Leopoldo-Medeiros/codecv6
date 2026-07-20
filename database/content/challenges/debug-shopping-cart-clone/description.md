## The situation

The checkout has a "duplicate order with a better discount" feature:
`cloneCartWithNewDiscount()` copies the cart and sets a new percentage on the
copy. QA's bug report is the spooky kind: **changing the clone's discount
also changed the original cart's discount** — two carts, one discount,
apparently entangled.

Nothing supernatural: PHP's `clone` is **shallow**. The clone gets its own
scalar properties, but any property holding an *object* still points at the
**same** object as the original. Two carts, one shared `Discount` instance —
until `__clone()` says otherwise.

## Your task

The tests are already written and one currently **fails**:

| **After cloning with 25.0** | **Expected** |
| clone's discount | `25.0` |
| original's discount | still `10.0` |

Fix the cart so cloning produces a truly independent copy.

## Hints

- **Hint 1:** the empty `__clone()` method in the boilerplate isn't decoration — it's the hook PHP calls *on the copy* right after a shallow clone. Deep-copy the nested object there: `$this->discount = clone $this->discount;` (guard for `null`).
- **Hint 2:** prove the diagnosis before fixing: `var_dump($cart->discount === $newCart->discount)` — `true` means one shared instance, and `===` on objects is exactly the "same instance?" question.

## In the real world

Shared-mutable-object bugs are among the hardest to trace because the write
that corrupts your data lives in code operating on "a different" variable.
They bite in ORMs (`replicate()` on a Laravel model shallow-copies loaded
relations), in DTOs cached between requests, and anywhere a "template" object
gets customized per use. Two defenses beat `__clone()` bookkeeping in modern
PHP: make value-like objects **immutable** (`readonly` properties — a
`Discount` that can't change can be shared safely), or replace instead of
mutate (`$clone->discount = new Discount(25.0)`). Reserve deep-clone for when
you genuinely need mutable copies — and then `__clone()` is the tool.
