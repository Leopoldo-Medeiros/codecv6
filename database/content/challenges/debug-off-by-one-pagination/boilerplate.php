<?php

function paginate(array $items, int $page, int $perPage): array
{
    $offset = $page * $perPage;

    return array_slice($items, $offset, $perPage);
}
