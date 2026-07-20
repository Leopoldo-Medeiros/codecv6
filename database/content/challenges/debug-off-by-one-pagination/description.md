## The situation

QA's ticket against the `paginate()` helper: *"Page 1 of the product list is
missing the first 3 items. Every page shows items that belong on the next
page instead."*

Read that symptom like a detective: nothing crashes, no items are lost from
the dataset, everything is just **shifted by exactly one page**. A uniform
shift means the math is consistent — and consistently wrong. The offset
calculation is treating page numbers as if they started at 0, but humans
(and the API contract) start counting at 1.

## Your task

The tests are already written and currently **fail**:

| **Call** on `range(1, 10)` | **Expected** |
| `paginate($items, 1, 3)` | `[1, 2, 3]` |
| `paginate($items, 2, 3)` | `[4, 5, 6]` |
| `paginate($items, 4, 3)` | `[10]` — last, partial page |

Fix the function. Do not rewrite it — the fix is a small, targeted change to
the offset formula.

## Hints

- **Hint 1:** compute the offset by hand for page 1: the current formula gives `1 × 3 = 3`, so `array_slice` starts at index 3 — skipping items 0–2. What should the offset be for page 1? For page 2?
- **Hint 2:** the canonical formula for 1-based pages is `($page - 1) * $perPage`. You'll write it a hundred more times in your career; today is when it sticks.

## In the real world

`OFFSET = (page - 1) * per_page` is the exact SQL every paginator generates —
Laravel's `->paginate()` does this translation for you, which is why
hand-rolled pagination is where the bug reappears (report exports, cron
batches, API cursors). The deeper habit: when output is *systematically*
shifted rather than randomly wrong, hunt for a 0-based/1-based disagreement
at a boundary — pages, months (JavaScript's `Date` months start at 0),
spreadsheet rows, "week numbers". Fencepost bugs are all the same bug wearing
different clothes.
