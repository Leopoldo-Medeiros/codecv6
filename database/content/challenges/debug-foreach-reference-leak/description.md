## The situation

`normalizeNames()` tidies user-submitted names — trim, lowercase, capitalize.
It has two loops: the normalizer, and an empty leftover loop where debug
logging used to be. Removing the logging looked like a safe cleanup; the
empty loop "obviously" does nothing.

The bug report says otherwise: *"the last two names keep coming out
identical, no matter what we submit."* Welcome to PHP's most notorious
gotcha — the **dangling foreach reference**.

## Your task

The tests are already written and one currently **fails**:

| **Input** | **Expected** |
| `['ALICE', ' bob ', 'CAROL']` | `['Alice', 'Bob', 'Carol']` |
| `['one', 'two', 'three', 'four']` | `['One', 'Two', 'Three', 'Four']` — no duplicated tail |

Find the bug and fix it — the fix is a single line, and it is **not** inside
either loop's body.

## Hints

- **Hint 1:** after `foreach ($names as &$name)` ends, `$name` is still a live reference to the **last** element. The second loop then assigns each element *into that reference* — every iteration overwrites the array's last slot, and the final iteration writes the second-to-last value there.
- **Hint 2:** the canonical fix is `unset($name);` immediately after the by-reference loop. (Also legitimate: drop the reference entirely and build a new array with `array_map` — references you don't strictly need are references you'll eventually debug.)

## In the real world

This gotcha has its own section in the PHP manual and decades of bug reports
— it survives because the crime scene (the harmless second loop) is far from
the cause (the loop that ended twenty lines earlier). Static analyzers flag
it, and PHP's own RFC discussions have called the behavior regrettable but
unfixable for BC. The transferable habits: always `unset` a by-reference
loop variable, prefer `array_map`/`array_walk` over reference loops for pure
transforms, and when the *tail* of a dataset corrupts mysteriously, ask what
variable outlived its loop.
