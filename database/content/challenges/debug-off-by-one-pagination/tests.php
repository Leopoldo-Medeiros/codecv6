<?php

use PHPUnit\Framework\TestCase;

class PaginateTest extends TestCase
{
    public function test_first_page_returns_the_first_items(): void
    {
        $items = range(1, 10);

        $this->assertSame([1, 2, 3], array_values(paginate($items, 1, 3)));
    }

    public function test_second_page_returns_the_next_items(): void
    {
        $items = range(1, 10);

        $this->assertSame([4, 5, 6], array_values(paginate($items, 2, 3)));
    }

    public function test_last_partial_page_returns_the_remaining_item(): void
    {
        $items = range(1, 10);

        $this->assertSame([10], array_values(paginate($items, 4, 3)));
    }
}
