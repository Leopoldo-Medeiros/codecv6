<?php

use PHPUnit\Framework\TestCase;

class FindApiBottleneckTest extends TestCase
{
    public function test_returns_every_product_with_its_category(): void
    {
        ProductDatabase::$queries = 0;
        $result = listProductsWithCategories(new ProductDatabase());

        $this->assertCount(60, $result);
        // Products 1/2/3 map to categories 1/2/3, then the pattern repeats.
        $this->assertSame('Product 1', $result[0]['name']);
        $this->assertSame('Electronics', $result[0]['category']);
        $this->assertSame('Books', $result[1]['category']);
        $this->assertSame('Clothing', $result[2]['category']);
        // ...and it holds after the cycle repeats.
        $this->assertSame('Electronics', $result[3]['category']);
    }

    public function test_hits_the_database_a_small_constant_number_of_times(): void
    {
        ProductDatabase::$queries = 0;
        listProductsWithCategories(new ProductDatabase());

        // The N+1 version fires 60 queries (one per product). A batched version
        // fires a small constant — 1 or 2 — no matter how many products there are.
        $this->assertLessThan(3, ProductDatabase::$queries);
    }
}
