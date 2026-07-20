## The situation

Your product listing page takes seconds to load and customers are noticing.
You open the trace and there it is: a wall of ~60 near-identical database
spans, each a few milliseconds, stacking into a slow request. The endpoint is
querying the database **once per product** just to fetch each one's category
name — the classic **N+1 query**.

The telemetry already did the diagnosis for you. Now comes the fix.

## Your task

Rewrite `listProductsWithCategories()` so it hits the database a **constant**
number of times — batch the category lookups with the provided
`findCategories()` method — while returning **exactly the same result**:
every product, each with its resolved category name.

Rules of the game:

- `ProductDatabase` is the database — do not modify it. Its `$queries` counter
  is your query log.
- `findCategory(int $id)` is one round-trip per call (the N+1 trap you're
  removing); `findCategories(array $ids)` is one round-trip for many ids
  (your `WHERE id IN (…)`).
- The result must be identical to the naive version: 60 products, categories
  cycling Electronics / Books / Clothing.

## Examples

| **Implementation** | **Queries fired** | **Trace looks like** |
| naive loop with `findCategory()` | 60 | a wall of identical spans |
| batched with `findCategories()` | 1–2 | one clean span |

The test asserts `ProductDatabase::$queries < 3` — same output, a fraction of
the round-trips.

## Hints

- **Hint 1:** collect the category ids you'll need first (`array_column($products, 'category_id')`), deduplicate, then make **one** batched call before the loop.
- **Hint 2:** `findCategories()` returns a map of `id => name` — build the product rows by looking up in that map, not by calling the database again.
- **Hint 3:** if your query counter reads exactly 60, you translated the loop but kept the per-item call inside it. The batched call belongs *before* the loop, not in it.

## In the real world

This is Laravel's `Product::with('category')` — eager loading — versus lazy
loading inside a Blade loop. The N+1 is the single most common performance bug
in ORM codebases, and the way you *found* it here matters as much as the fix:
you read the trace instead of guessing. The Observability 101 path opens with
this exact incident ("checkout is getting slower") diagnosed from a live span
waterfall — this challenge is the hands-on half of that lesson.
