<?php

use PHPUnit\Framework\TestCase;

class OptimizeEndpointTest extends TestCase
{
    private array $catalog;

    protected function setUp(): void
    {
        $this->catalog = [
            ['id' => 1, 'name' => 'Laptop', 'price' => 120000, 'category' => 'electronics', 'active' => true],
            ['id' => 2, 'name' => 'Phone', 'price' => 80000, 'category' => 'electronics', 'active' => true],
            ['id' => 3, 'name' => 'Shirt', 'price' => 3000, 'category' => 'clothing', 'active' => true],
            ['id' => 4, 'name' => 'Book', 'price' => 1500, 'category' => 'books', 'active' => false],
            ['id' => 5, 'name' => 'Tablet', 'price' => 50000, 'category' => 'electronics', 'active' => true],
        ];
    }

    public function test_filters_by_category(): void
    {
        $result = getFilteredProducts($this->catalog, ['category' => 'electronics']);

        $this->assertCount(3, $result);
        foreach ($result as $item) {
            $this->assertSame('electronics', $item['category']);
        }
    }

    public function test_filters_out_inactive_products(): void
    {
        $result = getFilteredProducts($this->catalog, []);

        foreach ($result as $item) {
            $this->assertNotSame(4, $item['id']);
        }
    }

    public function test_sorts_by_price_ascending(): void
    {
        $result = getFilteredProducts($this->catalog, ['sort' => 'price_asc']);

        $this->assertLessThanOrEqual($result[1]['price'], $result[2]['price']);
    }

    public function test_returns_all_active_when_no_filters(): void
    {
        $result = getFilteredProducts($this->catalog, []);

        $this->assertCount(4, $result);
    }

    public function test_empty_catalog_returns_empty(): void
    {
        $result = getFilteredProducts([], []);
        $this->assertSame([], $result);
    }
}
