<?php

use PHPUnit\Framework\TestCase;

class DebugSessionTest extends TestCase
{
    public function test_bug1_payment_processed_once(): void
    {
        $processor = new PaymentProcessor();
        $result = $processor->processPayment(1, 5000);

        $this->assertSame(5000, $result['total_charged']);
    }

    public function test_bug2_discount_calculated_correctly(): void
    {
        $calc = new DiscountCalculator();

        $item = ['price' => 10000, 'discount_percent' => 20];
        $this->assertSame(2000, $calc->calculateDiscount($item));
    }

    public function test_bug2_no_discount_when_percent_zero(): void
    {
        $calc = new DiscountCalculator();

        $item = ['price' => 10000, 'discount_percent' => 0];
        $this->assertSame(0, $calc->calculateDiscount($item));
    }

    public function test_bug3_email_sent_to_correct_user(): void
    {
        $notifier = new OrderNotifier();

        $user1 = new class { public string $email = 'alice@example.com'; };
        $user2 = new class { public string $email = 'bob@example.com'; };

        $result1 = $notifier->sendConfirmation($user1, 1);
        $result2 = $notifier->sendConfirmation($user2, 2);

        $this->assertSame('alice@example.com', $result1['sent_to']);
        $this->assertSame('bob@example.com', $result2['sent_to']);
    }
}
