<?php

use PHPUnit\Framework\TestCase;

class OrderStatusTest extends TestCase
{
    public function test_pending_label_and_color(): void
    {
        $this->assertSame('Pending payment', OrderStatus::Pending->label());
        $this->assertSame('yellow', OrderStatus::Pending->color());
    }

    public function test_paid_label_and_color(): void
    {
        $this->assertSame('Paid', OrderStatus::Paid->label());
        $this->assertSame('green', OrderStatus::Paid->color());
    }

    public function test_cancelled_label_and_color(): void
    {
        $this->assertSame('Cancelled', OrderStatus::Cancelled->label());
        $this->assertSame('red', OrderStatus::Cancelled->color());
    }

    public function test_from_string_returns_correct_case(): void
    {
        $this->assertSame(OrderStatus::Paid, OrderStatus::from('paid'));
    }
}
