<?php

class Attribute
{
    public function __construct(
        public string $name,
        public mixed $value,
    ) {}
}

class Span
{
    /** @param array<string, Attribute> $attributes */
    public function __construct(
        private array $attributes = [],
    ) {}

    public function getAttribute(string $name): ?Attribute
    {
        return $this->attributes[$name] ?? null;
    }
}

class Transaction
{
    public function __construct(
        public readonly string $id,
        public ?Span $currentSpan = null,
    ) {}
}

function getCustomAttributeValue(Transaction $transaction, string $name): mixed
{
    // BUG: crashes with a fatal error when currentSpan is null
    // or when the attribute does not exist on the span.
    // Fix this using the null-safe operator ?->
    return $transaction->currentSpan->getAttribute($name)->value;
}
