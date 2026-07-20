## The situation

`hasPermission()` checks whether a user's permission list contains a given
permission. It sailed through testing — then production threw "permission
denied" at a user who *visibly had the permission*. The pattern in the bug
report is the giveaway: it only happens when the permission being checked is
the **first one in the user's list**.

A function that fails only for the first element isn't confused about the
values. It's confused about **position zero**.

## Your task

The tests are already written and one currently **fails**:

| **List** | **Check** | **Expected** |
| `['edit-posts', 'delete-posts']` | `edit-posts` (index 0) | `true` |
| `['view-posts', 'edit-posts']` | `edit-posts` (index 1) | `true` |
| `['view-posts']` | `delete-posts` | `false` |

Find the bug and fix it — the fix is a single operator, not a rewrite.

## Hints

- **Hint 1:** `array_search` returns the **index** on success and `false` on failure. What's the index of the first element — and what does that value become inside a truthiness check?
- **Hint 2:** two idiomatic fixes: compare strictly (`array_search(...) !== false`) or use the function that answers the actual question being asked — `in_array($permission, $permissions, true)`.

## In the real world

`0 == false` is the classic PHP falsy trap, and `array_search`/`strpos` are
its two most famous carriers — `strpos($haystack, $needle)` returning `0` for
"found at the start" has broken uncountable `if (strpos(...))` checks. Modern
PHP gives you needle functions that return booleans (`str_contains`,
`str_starts_with`) precisely to kill this bug class. The habits that
generalize: use `!==`/`===` whenever a return type mixes meaningful values
with `false`, and prefer the function whose return type matches the question
— you wanted a yes/no, so call the one that returns a bool.
