<?php

use PHPUnit\Framework\TestCase;

class MoneyValueObjectTest extends TestCase
{
    public function test_add_returns_new_instance_with_correct_amount(): void
    {
        $a      = new Money(1000, 'EUR');
        $b      = new Money(500,  'EUR');
        $result = $a->add($b);

        $this->assertInstanceOf(Money::class, $result);
        $this->assertSame(1500, $result->amount);
    }

    public function test_add_does_not_mutate_original(): void
    {
        $a = new Money(1000, 'EUR');
        $b = new Money(500,  'EUR');
        $a->add($b);

        $this->assertSame(1000, $a->amount);
    }

    public function test_add_throws_for_mismatched_currencies(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new Money(1000, 'EUR'))->add(new Money(500, 'BRL'));
    }

    public function test_to_float_converts_cents_to_major_units(): void
    {
        $this->assertSame(12.5, (new Money(1250, 'EUR'))->toFloat());
    }

    public function test_format_eur(): void
    {
        $this->assertSame('€10.00', (new Money(1000, 'EUR'))->format());
    }

    public function test_format_brl(): void
    {
        $this->assertSame('R$99.90', (new Money(9990, 'BRL'))->format());
    }
}
