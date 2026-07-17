<?php

/**
 * @param list<array{id: int, name: string, price: int, category: string, active: bool}> $products
 * @return list<array{id: int, name: string, price: int, category: string}>
 */
function getFilteredProducts(array $products, array $filters): array
{
    // BUG: loads everything, doesn't filter by category, doesn't sort by price
    $result = [];
    foreach ($products as $p) {
        $result[] = [
            'id' => $p['id'],
            'name' => $p['name'],
            'price' => $p['price'],
            'category' => $p['category'],
        ];
    }
    return $result;
}
