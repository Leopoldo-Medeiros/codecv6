<?php

class ValidateOrderTest extends \PHPUnit\Framework\TestCase
{
    public function test_a_valid_payload_has_no_errors()
    {
        $this->assertCount(0, validateOrder(['product_id' => 5, 'quantity' => 2, 'currency' => 'eur']));
    }

    public function test_missing_product_id_is_rejected()
    {
        $this->assertGreaterThan(0, count(validateOrder(['quantity' => 2, 'currency' => 'eur'])));
    }

    public function test_quantity_out_of_range_is_rejected()
    {
        $this->assertGreaterThan(0, count(validateOrder(['product_id' => 5, 'quantity' => 0, 'currency' => 'eur'])));
        $this->assertGreaterThan(0, count(validateOrder(['product_id' => 5, 'quantity' => 101, 'currency' => 'eur'])));
    }

    public function test_unsupported_currency_is_rejected()
    {
        $this->assertGreaterThan(0, count(validateOrder(['product_id' => 5, 'quantity' => 2, 'currency' => 'usd'])));
    }
}
