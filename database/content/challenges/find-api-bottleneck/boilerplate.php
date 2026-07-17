<?php

/**
 * A stand-in for your database. Every method here is one round-trip — exactly
 * what an ORM does under the hood. Do NOT change this class: it IS the database,
 * and the whole point is to make your code hit it fewer times.
 */
class ProductDatabase
{
    /** How many queries your code has triggered so far. */
    public static int $queries = 0;

    private array $categories = [
        1 => 'Electronics',
        2 => 'Books',
        3 => 'Clothing',
    ];

    /** One query for ONE category — this is what an N+1 leans on. */
    public function findCategory(int $id): string
    {
        self::$queries++;

        return $this->categories[$id] ?? 'Unknown';
    }

    /** One query for MANY categories — the batched way. */
    public function findCategories(array $ids): array
    {
        self::$queries++;

        $found = [];
        foreach ($ids as $id) {
            $found[$id] = $this->categories[$id] ?? 'Unknown';
        }

        return $found;
    }
}

/** The product listing — imagine 60 rows straight from the database. */
function products(): array
{
    $products = [];
    for ($i = 1; $i <= 60; $i++) {
        $products[] = [
            'id' => $i,
            'name' => "Product {$i}",
            'category_id' => (($i - 1) % 3) + 1,
        ];
    }

    return $products;
}

/**
 * Attach each product's category name.
 *
 * RIGHT NOW this calls the database once PER product — a classic N+1. With 60
 * products that's 60 queries, and the trace is a wall of identical spans.
 *
 * TODO: rewrite this to hit the database a constant number of times (use
 * findCategories) while returning the exact same result, in the same order.
 */
function listProductsWithCategories(ProductDatabase $db): array
{
    $result = [];

    foreach (products() as $product) {
        $result[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'category' => $db->findCategory($product['category_id']),
        ];
    }

    return $result;
}
