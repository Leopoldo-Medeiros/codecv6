## The situation

The API gateway team asked for a tiny thing: human-readable labels for status
codes in the request log dashboard. The previous attempt was a `switch` that
someone extended under deadline — a missing `break` made every `401` log as
`"Bad Request"` for a month before anyone noticed. Fall-through strikes
again.

PHP 8.0's `match` expression is the post-mortem's recommendation: strict
comparison, returns a value, **no fall-through possible**, and unknown input
throws instead of sliding silently into the wrong arm.

## Your task

Implement `statusLabel(int $code): string` with a single `match` expression.
Do **not** add a `default` arm — an unknown code must throw
`\UnhandledMatchError` (the tests feed it `418` and expect the explosion).

| **Code** | **Label** |
| 200 | `OK` |
| 201 | `Created` |
| 400 | `Bad Request` |
| 401 | `Unauthorized` |
| 403 | `Forbidden` |
| 404 | `Not Found` |
| 422 | `Unprocessable Entity` |
| 500 | `Internal Server Error` |

## Hints

- **Hint 1:** each arm is `404 => 'Not Found',` — expression in, value out, comma-separated. The whole function body is `return match ($code) { … };`.
- **Hint 2:** the no-`default` rule is the exercise's point, not an oversight: "throw on unknown" turns *silent wrong data* into a *loud, findable bug*. You choose per call site whether that trade is right; here it is.

## In the real world

`match` vs `switch` is more than syntax: `switch` compares loosely
(`'200' == 200` — true) and falls through on a forgotten `break`; `match`
does neither. The no-default discipline is the same one behind exhaustive
enum matching (see the status-enum challenge: add a case, get an error at
every unhandled `match`). And a Laravel note for real code: framework
helpers like `Response::$statusTexts` already know these labels — the kata's
value isn't the table, it's making *unhandled input loud*, a habit worth
applying to every mapping function you write.
