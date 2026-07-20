## The situation

The nightly export job died again: *"Allowed memory size of 536870912 bytes
exhausted."* The culprit is honest, straightforward code — it loads **all**
subscribers into an array, then writes the CSV. It worked at 10k rows. At
2 million, the array alone is the outage.

The fix isn't more memory; it's changing the *shape* of the data flow.
A PHP **generator** yields one row at a time and only fetches the next page
when the consumer asks — the export becomes a stream, and peak memory stays
at one page no matter how large the table grows.

## Your task

Implement `paginatedRows(callable $fetchPage, int $perPage = 100): \Generator`:

- Call `$fetchPage($page, $perPage)` starting from page 1.
- **Yield** each row of the page individually.
- Fetch page N+1 only after page N's rows are consumed.
- **Stop** when `$fetchPage` returns an empty array — including an empty
  *first* page (empty table → yields nothing, no crash).

```php
foreach (paginatedRows($fetchFromDb, perPage: 500) as $row) {
    process($row);   // never more than 500 rows in memory
}
```

## Examples

| **Scenario** | **Behaviour** |
| 5 rows, `perPage: 2` | yields all 5, in order, across 3 fetches |
| page 1 has data, page 2 empty | exactly 2 calls to `$fetchPage` — no over-fetching |
| empty table | yields nothing |

## Hints

- **Hint 1:** the skeleton is a `while (true)` with three moves: fetch, `if (empty($rows)) return;`, then `foreach ($rows as $row) yield $row;` and `$page++`.
- **Hint 2:** the presence of `yield` anywhere in the function body is what makes PHP treat it as a generator — the function returns a `\Generator` you can `foreach`, without you constructing one.
- **Hint 3:** `yield from $rows` collapses the inner foreach — but beware: `yield from` reuses the inner array's keys. The test collects with `iterator_to_array($gen, false)`, so you're safe either way here — knowing *why* is the senior detail.

## In the real world

This is exactly what Laravel's `Model::lazy()` and `cursor()` do — `lazy()`
even paginates underneath, like yours — and why `chunk()` exists for the
callback style. The pattern applies to every unbounded source: paginated
HTTP APIs (yield results page by page while the caller streams), CSV
imports, S3 listings. One caveat worth carrying: generators are
single-pass — you can't rewind or count them without consuming them, so
they're for *flows*, not for data you need to revisit. When the memory graph
of a job looks like a staircase to a crash, the answer is almost always
"stop materializing, start streaming."
