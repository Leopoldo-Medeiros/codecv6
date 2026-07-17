<?php

use PHPUnit\Framework\TestCase;

class ValidateQuantityTest extends TestCase
{
    public function test_missing_quantity_is_invalid(): void
    {
        $this->assertCount(1, validateQuantity([]));
    }

    public function test_quantity_of_zero_is_valid(): void
    {
        $this->assertCount(0, validateQuantity(['quantity' => '0']));
    }

    public function test_quantity_present_is_valid(): void
    {
        $this->assertCount(0, validateQuantity(['quantity' => '5']));
    }
}
