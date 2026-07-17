<?php

/**
 * Return a paginated JSON envelope for an API list endpoint:
 *
 *   [
 *     'data' => $items,
 *     'meta' => [
 *       'current_page' => int,
 *       'per_page'     => int,
 *       'total'        => int,
 *       'last_page'    => int,   // ceil(total / perPage), never less than 1
 *     ],
 *   ]
 *
 * @param array $items  the current page of records (already sliced)
 */
function paginate(array $items, int $page, int $perPage, int $total): array
{
    // TODO: build and return the envelope described above.
    return [];
}
