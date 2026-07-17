<?php

use PHPUnit\Framework\TestCase;

class SortByPriorityTest extends TestCase
{
    public function test_orders_high_before_medium_before_low(): void
    {
        $tickets = [
            ['title' => 'A', 'priority' => 'low'],
            ['title' => 'B', 'priority' => 'high'],
            ['title' => 'C', 'priority' => 'medium'],
        ];

        $sorted = sortByPriority($tickets);

        $this->assertSame(['B', 'C', 'A'], array_column($sorted, 'title'));
    }
}
