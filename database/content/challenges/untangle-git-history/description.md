## The situation

Two developers edited `config/production.php` on separate branches. One
updated the database credentials for the new cluster; the other added an API
key and flipped a debug flag. Then someone resolved the merge conflict by
force-pushing — and now the deploy is broken because the "resolution" simply
threw one side away.

Git offers merge strategies for exactly this (`-X ours`, `-X theirs`), and the
best way to stop fearing them is to implement their semantics yourself. You're
writing the resolver that the panicked force-pusher needed.

## Your task

Implement `mergeConfigs(array $ours, array $theirs, string $strategy): array`.
Every strategy produces the **union of both sides' keys** — a key that exists
on only one side always survives (that's what a merge is). The strategy only
decides who wins **when both sides define the same key**:

- `'ours'` — our value wins conflicts; their unique keys still come along.
- `'theirs'` — their value wins conflicts; our unique keys still come along.
- `'combine'` — explicit union, resolving conflicts in our favor (the
  hand-written equivalent of `git merge -X ours`).

Empty inputs behave sanely: two empty arrays merge to `[]`; an empty side
contributes nothing but blocks nothing.

## Examples

With `ours = ['db_host' => 'localhost', 'cache_ttl' => 3600]` and
`theirs = ['db_host' => 'prod-db.internal', 'api_key' => 'secret123']`:

| **Strategy** | **db_host** (conflict) | **api_key** (theirs only) | **cache_ttl** (ours only) |
| `ours` | `localhost` | `secret123` | `3600` |
| `theirs` | `prod-db.internal` | `secret123` | `3600` |
| `combine` | `localhost` | `secret123` | `3600` |

## Hints

- **Hint 1:** PHP's `+` operator on arrays keeps the **left** side's value on key collisions — `$ours + $theirs` is the entire `'ours'` strategy in one expression.
- **Hint 2:** `'theirs'` is the same expression with the operands swapped. Don't reach for `array_merge` without checking what it does to duplicate string keys (right side wins) — knowing the difference between `+` and `array_merge` is half this kata.
- **Hint 3:** a `match ($strategy)` expression keeps the three branches honest and exhaustive.

## In the real world

`git merge -X ours` and `-X theirs` do per-conflict resolution exactly like
this — they do **not** discard the other branch wholesale (that's `git merge
-s ours`, a different and more dangerous thing; confusing the two is a classic
incident). The same union-with-priority pattern shows up everywhere in config
management: Laravel's `config()` cascading over environment defaults,
`array_replace_recursive` in package options, Kubernetes strategic merge
patches. Learn it once here, recognize it everywhere.
