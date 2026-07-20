## The situation

The `/products` endpoint takes 11 seconds in production. The APM trace shows
why: it loads **the entire catalog** — 100k+ rows — into PHP, then the
frontend filters what it needs client-side. Memory spikes, response payloads
are megabytes, and the ops channel is asking why the pods keep OOM-ing.

The fix in real life is "push filtering and sorting into the database." This
challenge simulates exactly that: `getFilteredProducts()` stands in for your
query layer, and your job is to make it return **only what the client asked
for, already sorted** — the work a `WHERE` and an `ORDER BY` would do.

## Your task

Fix `getFilteredProducts(array $products, array $filters): array` so that it:

- **Excludes inactive products, always** — `active === false` never leaves the
  data layer, filters or not (soft-deleted rows don't ship to clients).
- **Filters by category** when `$filters['category']` is present.
- **Sorts by price ascending** when `$filters['sort'] === 'price_asc'`.
- **Strips the `active` key** from each returned row — the response shape is
  `id`, `name`, `price`, `category` only (internal flags stay internal).
- Returns a **re-indexed list** (`0, 1, 2, …`) — and `[]` for an empty catalog.

## Examples

Using the 5-product test catalog (4 active, 1 inactive book):

| **Filters** | **Result** |
| `['category' => 'electronics']` | 3 rows, all electronics |
| `[]` | 4 rows — every active product, the inactive book gone |
| `['sort' => 'price_asc']` | active products, cheapest first |
| empty catalog, any filters | `[]` |

## Hints

- **Hint 1:** `array_filter()` preserves keys — after filtering, positions `[1]` and `[2]` may not exist. `array_values()` re-indexes; the sorting test reads `$result[1]` and `$result[2]` directly.
- **Hint 2:** `usort($rows, fn ($a, $b) => $a['price'] <=> $b['price'])` — the spaceship operator is the idiomatic comparator.
- **Hint 3:** think in stages, like SQL executes: filter (`WHERE active AND category = ?`), then sort (`ORDER BY price`), then shape the output (`SELECT id, name, price, category`).

## In the real world

Every stage you just wrote has a database twin: the active check is a global
scope (`Model::addGlobalScope`) or soft deletes, the category filter is a
`where()`, the sort is an `orderBy()`, and the key-stripping is an API
Resource's `toArray()`. When you see an endpoint doing `Model::all()` followed
by PHP-side `array_filter`, you're looking at this challenge in the wild —
and the fix is always the same: move the work to the database, which has
indexes, and return only the page you need (see the pagination challenge).
