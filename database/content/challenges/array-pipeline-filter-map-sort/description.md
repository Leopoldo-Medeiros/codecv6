## The situation

Marketing wants a "premium picks" block in the newsletter: active products
over €50, shown as tidy `"Name — €150.00"` lines, alphabetized. The current
implementation is a 30-line `foreach` with three nested `if`s, a manual
counter, and a sort bolted on at the end — it works, but every new rule
makes it worse, and the review comment is already there: *"this is a
filter, a map and a sort — write it as one."*

Thinking in pipeline stages instead of loop iterations is the single most
transferable data-handling habit in PHP — it's how SQL thinks, how
collections think, and how your future teammates will read code.

## Your task

Implement `formatPremiumProducts(array $products): array` as a three-stage
pipeline over rows of `['name', 'price_cents', 'active']`:

- **Filter** — keep only `active === true` **and** `price_cents > 5000`
  (strictly above the €50.00 threshold: a product at exactly 5000 is out).
- **Map** — each survivor becomes the string `"Name — €X.XX"`, price in
  euros with two decimals.
- **Sort** — alphabetically by product name, ascending.

Return a re-indexed list of strings (`[]` for empty input).

## Examples

| **Input row** | **Fate** |
| `Widget B, 15000, active` | `'Widget B — €150.00'` |
| `Widget A, 3000, active` | dropped — too cheap |
| `Widget C, 20000, inactive` | dropped — inactive |
| `Widget E, 5000, active` | dropped — at the threshold, not above it |

Full expected output for the test catalogue:
`['Widget B — €150.00', 'Widget D — €300.00']`.

## Hints

- **Hint 1:** stage order matters for cheap-vs-correct: filter first (fewer rows to format), and sort *before* mapping if you sort by a field the map throws away — or sort the strings after, which here happens to coincide with sorting by name. Pick one deliberately.
- **Hint 2:** `array_filter` keeps original keys — the test compares with `assertSame` against a clean list, so finish with `array_values()`.
- **Hint 3:** `number_format($cents / 100, 2, '.', '')` renders `150.00` — the fourth argument kills the thousands separator that would sneak a comma into `€1,500.00`.

## In the real world

This pipeline is `collect($products)->filter(…)->map(…)->sortBy(…)->values()`
in Laravel — and `WHERE active AND price_cents > 5000 … ORDER BY name` in
SQL, which is where this logic *should* live once the data source is a table
(see the optimize-api-endpoint challenge for that migration). The habits
that transfer: keep each stage a pure single-purpose function, know the
`array_filter` key-preservation gotcha (it pairs with `array_merge`'s
key-renumbering as PHP's two classic array-key surprises), and treat
money-as-cents until the final formatting step — the same rule the Money
value-object challenge enforces.
