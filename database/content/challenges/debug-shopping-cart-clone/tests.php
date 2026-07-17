<?php

use PHPUnit\Framework\TestCase;

class ShoppingCartCloneTest extends TestCase
{
    public function test_cloned_cart_gets_the_new_discount(): void
    {
        $discount = new Discount(10.0);
        $cart = new ShoppingCart();
        $cart->discount = $discount;

        $newCart = cloneCartWithNewDiscount($cart, 25.0);

        $this->assertSame(25.0, $newCart->discount->percentage);
    }

    public function test_original_cart_discount_is_unaffected(): void
    {
        $discount = new Discount(10.0);
        $cart = new ShoppingCart();
        $cart->discount = $discount;

        cloneCartWithNewDiscount($cart, 25.0);

        $this->assertSame(10.0, $cart->discount->percentage);
    }
}
