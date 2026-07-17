## Memory-Efficient Generator Pagination

Loading millions of rows from a database into an array will exhaust memory. PHP **generators** solve this by yielding rows one at a time — the next page is only fetched when the consumer asks for it.

**Goal:** implement `paginatedRows()` as a `Generator` that:
- Calls `$fetchPage(int $page, int $perPage)` starting from page 1
- **Yields** each row from the page individually
- Fetches the next page only after the current page is exhausted
- **Stops** when `$fetchPage` returns an empty array

```php
$rows = paginatedRows(fn($page, $per) => fetchFromDb($page, $per), perPage: 500);
foreach ($rows as $row) {
    process($row); // never holds more than 500 rows in memory
}
```
