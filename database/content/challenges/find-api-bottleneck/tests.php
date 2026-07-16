<?php

use PHPUnit\Framework\TestCase;

class BottleneckTest extends TestCase
{
    public function test_returns_correct_products_with_categories(): void
    {
        $result = getProductsWithCategories();

        $this->assertCount(4, $result);
        $this->assertSame('Electronics', $result[0]['category']);
        $this->assertSame('Books', $result[2]['category']);
        $this->assertSame('Clothing', $result[3]['category']);
    }

    public function test_batched_lookups_reduces_queries(): void
    {
        resetQueryCount();
        getProductsWithCategories();

        $this->assertLessThanOrEqual(1, getQueryCount());
    }

    public function test_product_names_are_correct(): void
    {
        $result = getProductsWithCategories();

        $names = array_map(fn($p) => $p['name'], $result);
        $this->assertContains('Laptop', $names);
        $this->assertContains('PHP Book', $names);
    }
}
