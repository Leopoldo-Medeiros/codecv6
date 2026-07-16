<?php

use PHPUnit\Framework\TestCase;

class StrategyPaymentTest extends TestCase
{
    public function test_stripe_gateway_succeeds(): void
    {
        $result = (new StripeGateway())->charge(1000, 'EUR');
        $this->assertTrue($result->success);
        $this->assertStringStartsWith('stripe_', $result->transactionId);
    }

    public function test_paypal_gateway_succeeds(): void
    {
        $result = (new PayPalGateway())->charge(1000, 'EUR');
        $this->assertTrue($result->success);
        $this->assertStringStartsWith('paypal_', $result->transactionId);
    }

    public function test_processor_delegates_to_stripe(): void
    {
        $result = (new PaymentProcessor(new StripeGateway()))->process(500, 'EUR');
        $this->assertTrue($result->success);
        $this->assertStringStartsWith('stripe_', $result->transactionId);
    }

    public function test_processor_delegates_to_paypal(): void
    {
        $result = (new PaymentProcessor(new PayPalGateway()))->process(500, 'EUR');
        $this->assertTrue($result->success);
        $this->assertStringStartsWith('paypal_', $result->transactionId);
    }

    public function test_processor_rejects_zero_amount(): void
    {
        $result = (new PaymentProcessor(new StripeGateway()))->process(0, 'EUR');
        $this->assertFalse($result->success);
        $this->assertSame('Amount must be positive', $result->error);
    }

    public function test_processor_rejects_negative_amount(): void
    {
        $result = (new PaymentProcessor(new StripeGateway()))->process(-100, 'EUR');
        $this->assertFalse($result->success);
    }
}
