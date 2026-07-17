## HTTP Status Labels with match

PHP 8.0 **match expressions** are the clean replacement for `switch`. Unlike `switch`, `match` uses strict comparison, returns a value directly, and throws `\UnhandledMatchError` for unmatched values — no more accidental fall-through.

**Goal:** implement `statusLabel()` using a `match` expression. Do **not** add a `default` arm — let unknown codes throw automatically.

| Code | Text |
|------|------|
| 200 | `OK` |
| 201 | `Created` |
| 400 | `Bad Request` |
| 401 | `Unauthorized` |
| 403 | `Forbidden` |
| 404 | `Not Found` |
| 422 | `Unprocessable Entity` |
| 500 | `Internal Server Error` |
