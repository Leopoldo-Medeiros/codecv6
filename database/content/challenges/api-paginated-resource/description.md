## The situation

Your team ships the company job board. The `/jobs` endpoint currently returns a
raw JSON array — all 3,400 rows, every request. The frontend team just opened a
ticket:

*"The jobs page takes 6 seconds on mobile and the infinite-scroll component
needs to know how many pages exist. Can every list endpoint return the same
paginated envelope, like the other APIs we consume?"*

They're right — and this shape is everywhere: Laravel's own paginator, Stripe's
`has_more`, GitHub's `Link` headers. Before reaching for the framework's
`paginate()`, you're going to build the envelope yourself so you understand
exactly what the framework does for you.

## Your task

Implement `paginate(array $items, int $page, int $perPage, int $total): array`
returning:

```json
{
  "data": [ "...the items you were given..." ],
  "meta": {
    "current_page": 2,
    "per_page": 10,
    "total": 45,
    "last_page": 5
  }
}
```

Requirements:

- `data` holds the items exactly as received — the caller already sliced the
  current page (in real life: the database did, via `LIMIT`/`OFFSET`).
- `meta.last_page` is `ceil(total / per_page)` — and **never less than 1**,
  even when `total` is 0. An empty result set still has one (empty) page;
  frontends divide by and iterate to `last_page`, and `0` breaks them.

## Examples

| **Call** | **`last_page`** | **Why** |
| `paginate($items, 2, 10, 45)` | `5` | ceil(45 / 10) |
| `paginate([], 1, 10, 0)` | `1` | empty ≠ zero pages |
| `paginate($items, 1, 25, 25)` | `1` | exact fit, no partial page |

## Hints

- **Hint 1:** stuck on the shape? Re-read the docblock in the boilerplate — the envelope is spelled out field by field.
- **Hint 2:** `ceil()` returns a `float` in PHP. The tests use `assertSame` with an `int` — cast the result.
- **Hint 3:** the "never less than 1" rule is one `max()` call.

## In the real world

Laravel's `LengthAwarePaginator` builds this exact meta block (plus links) for
you, and API Resources wrap it. Once you've built the envelope by hand, the
framework version stops being magic: you know why `last_page` exists, why
`total` comes from a separate `COUNT` query, and what breaks when a client
assumes zero-indexed pages.
