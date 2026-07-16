<?php

use PHPUnit\Framework\TestCase;

class SumRangeTest extends TestCase
{
    public function test_sum_of_one_is_one(): void
    {
        $this->assertSame(1, sumRange(1));
    }

    public function test_sum_of_five_is_fifteen(): void
    {
        $this->assertSame(15, sumRange(5));
    }
}
