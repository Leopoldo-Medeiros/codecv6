<?php

class Discount
{
    public function __construct(public float $percentage) {}
}

class ShoppingCart
{
    public array $items = [];

    public ?Discount $discount = null;

    public function __clone(): void
    {
    }
}

function cloneCartWithNewDiscount(ShoppingCart $original, float $newPercentage): ShoppingCart
{
    $clone = clone $original;
    $clone->discount->percentage = $newPercentage;

    return $clone;
}
