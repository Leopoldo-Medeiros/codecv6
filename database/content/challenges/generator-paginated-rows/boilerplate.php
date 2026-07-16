<?php

/**
 * Yields rows from a paginated source without loading all pages at once.
 *
 * @param  callable(int $page, int $perPage): array<mixed> $fetchPage
 * @return \Generator<int, mixed, void, void>
 */
function paginatedRows(callable $fetchPage, int $perPage = 100): \Generator
{
    // TODO:
    // - Start at page 1.
    // - Yield each row from fetchPage($page, $perPage).
    // - Increment $page and repeat.
    // - Stop when fetchPage returns an empty array.
}
