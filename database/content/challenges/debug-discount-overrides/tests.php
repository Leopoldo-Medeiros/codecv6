<?php

use PHPUnit\Framework\TestCase;

class ApplyDiscountOverridesTest extends TestCase
{
    public function test_override_replaces_the_matching_product_discount(): void
    {
        $result = applyDiscountOverrides([101 => 10, 102 => 15], [101 => 20]);

        $this->assertSame(20, $result[101]);
        $this->assertSame(15, $result[102]);
    }
}
