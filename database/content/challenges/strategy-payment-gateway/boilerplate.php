<?php

interface PaymentGateway
{
    public function charge(int $amountCents, string $currency): PaymentResult;
}

class PaymentResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $transactionId,
        public readonly ?string $error = null,
    ) {}
}

class StripeGateway implements PaymentGateway
{
    public function charge(int $amountCents, string $currency): PaymentResult
    {
        // TODO: return successful PaymentResult, transactionId starts with "stripe_"
    }
}

class PayPalGateway implements PaymentGateway
{
    public function charge(int $amountCents, string $currency): PaymentResult
    {
        // TODO: return successful PaymentResult, transactionId starts with "paypal_"
    }
}

class PaymentProcessor
{
    public function __construct(private PaymentGateway $gateway) {}

    public function process(int $amountCents, string $currency): PaymentResult
    {
        // TODO:
        // - If $amountCents <= 0, return failed PaymentResult with error "Amount must be positive"
        // - Otherwise delegate to $this->gateway
    }
}
