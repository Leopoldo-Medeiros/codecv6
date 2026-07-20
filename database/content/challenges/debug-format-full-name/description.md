## The situation

The consultant dashboard lists clients as `"Last, First"` — the standard
format for sorted directories, so "Doe, Jane" files under D. QA just flagged
that the whole list reads `"First, Last"` instead, which breaks the
alphabetical grouping the sidebar relies on.

`formatFullName()` is three lines. The bug survived review anyway — because
it's the kind that *looks* right when you read the code and only shows up when
you read the output.

## Your task

The test is already written and currently **fails**:

```php
$this->assertSame('Doe, Jane', formatFullName('Jane', 'Doe'));
```

Find the bug and fix it. No new variables needed — just make the output match
the contract.

## Hints

- **Hint 1:** read the `sprintf` template, then read the argument list, then say out loud which value lands in which `%s`.
- **Hint 2:** the function *signature* is fine — callers pass `(first, last)` correctly. The bug is inside.

## In the real world

Argument-order bugs are disproportionately common in formatting and
comparison code (`sprintf`, `str_replace($search, $replace, …)`, `strcmp`,
date formats) precisely because the types all match — nothing fails except
the meaning. Two habits prevent them: a test that asserts on a *realistic*
expected string (like this one), and named arguments
(`formatFullName(firstName: 'Jane', lastName: 'Doe')`) when a call site mixes
same-typed parameters. PHP 8 gave you the second one — use it.
