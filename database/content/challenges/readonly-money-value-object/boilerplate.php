<?php

class Money
{
    public function __construct(
        public readonly int $amount,      // in cents
        public readonly string $currency, // 'EUR' or 'BRL'
    ) {}

    public function add(Money $other): self
    {
        // TODO: return a new Money with combined amounts.
        // Throw \InvalidArgumentException if currencies differ.
    }

    public function toFloat(): float
    {
        // TODO: return amount / 100
    }

    public function format(): string
    {
        // TODO: '€' prefix for EUR, 'R$' prefix for BRL, two decimal places.
    }
}
