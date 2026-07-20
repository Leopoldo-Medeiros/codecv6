## The situation

The blog cards on the marketing site have a fixed-width layout, and the
designer sized them for previews of **at most `$maxLength` characters,
total**. QA reports every preview overflows by exactly three characters —
enough to wrap a line and break the grid.

`excerpt()` truncates and appends `...`. The report says "consistently a few
characters longer than the limit" — that word *consistently* is the clue: this
isn't random data, it's a systematic off-by-`N`.

## Your task

The test is already written and currently **fails**:

```php
$result = excerpt('This is a fairly long sentence that needs cutting', 20);
$this->assertSame(20, strlen($result));           // total length, ellipsis included
$this->assertStringContainsString('...', $result);
```

Fix `excerpt()` so the cap counts **everything it returns** — the cut text
*plus* the `...` — while short strings still pass through untouched.

## Examples

| **Call** | **Result length** |
| `excerpt('Hello', 10)` | 5 — short text unchanged, no ellipsis |
| `excerpt($longText, 20)` | exactly 20, ending in `...` |

## Hints

- **Hint 1:** the current code cuts the text to `$maxLength` and *then* appends three more characters. Budget the ellipsis inside the limit, not on top of it.
- **Hint 2:** careful with the early-return branch — a string of exactly `$maxLength` characters needs no ellipsis and must not lose its tail.

## In the real world

"Truncate to N" bugs ship constantly because the contract is ambiguous: N
*before* or *after* the suffix? Real APIs make it explicit — Laravel's
`Str::limit($text, 20)` counts the limit *before* the `...` (documented!), so
blindly swapping in the helper would re-introduce this exact overflow. The
lesson that outlives the kata: when a function decorates its output, write the
length assertion on the final string, and read the docs for where the helper
counts from. (Bonus thought: `strlen` counts bytes — multibyte text needs
`mb_*` functions, a sequel bug you'll meet in production eventually.)
