## The situation

A loyalty-points routine uses `sumRange($n)` to award the sum of 1..n bonus
points. Finance flagged a discrepancy: every payout is exactly **1 point
short**. Not random, not proportional — always one.

The function is recursive, and the recursive step reads perfectly:
`$n + sumRange($n - 1)`. That's the trap with recursive bugs: the part your
eye checks is fine. The lie is in the **base case** — the one place where the
recursion stops and truth is defined.

## Your task

The tests are already written and both currently **fail**:

| **Call** | **Expected** |
| `sumRange(1)` | `1` |
| `sumRange(5)` | `15` |

Find the bug and fix it — the fix is a single character in the base case.

## Hints

- **Hint 1:** unroll it by hand for `n = 2`: `2 + sumRange(1)` → `2 + ?`. What does the base case return for 1, and what *should* the sum of "1 to 1" be?
- **Hint 2:** a systematically-off-by-a-constant recursion is almost always a wrong base value; an off-by-a-*factor* points at the recursive step. Learn the smell and you'll debug these in seconds.

## In the real world

The discipline this kata teaches — **verify the base case first** — applies
far beyond recursion: loop initializations (`$total = 0` vs `$total = 1`),
reducer seeds (`array_reduce($items, $fn, 0)`), migration counters, paginated
API "start" tokens. When output is wrong by a constant, check where the
computation *starts*, not where it iterates. And note what saved you here: a
test asserting the smallest case (`sumRange(1)`), the case humans skip
because it feels too trivial to be wrong.
