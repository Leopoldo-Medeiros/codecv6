<?php

/**
 * @param  array<array{name: string, price_cents: int, active: bool}> $products
 * @return list<string>
 */
function formatPremiumProducts(array $products): array
{
    // TODO:
    // 1. Filter: active === true AND price_cents > 5000
    // 2. Map:    format as "Name — €X.XX"
    // 3. Sort:   alphabetically by name (ascending)
    // Return a re-indexed list of strings.
}
