<?php

use PHPUnit\Framework\TestCase;

class LaravelApiBugsTest extends TestCase
{
    public function test_bug1_order_not_duplicated(): void
    {
        $product = new Product(1, 'Widget', 10);
        $result = checkout($product, 1);
        $this->assertSame(1, $result['items']);
    }

    public function test_bug2_admin_cannot_delete_own_account(): void
    {
        $controller = new UserController('admin', 1);
        $result = $controller->deleteUser(1);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('own account', $result['error']);
    }

    public function test_bug2_non_admin_cannot_delete_own_account(): void
    {
        $controller = new UserController('user', 5);
        $result = $controller->deleteUser(5);
        $this->assertArrayHasKey('error', $result);
    }

    public function test_bug2_admin_can_delete_other_users(): void
    {
        $controller = new UserController('admin', 1);
        $result = $controller->deleteUser(2);
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
    }

    public function test_bug3_out_of_stock_returns_false(): void
    {
        $product = new Product(1, 'Widget', 0);
        $this->assertFalse($product->isInStock());
    }

    public function test_bug3_in_stock_returns_true(): void
    {
        $product = new Product(1, 'Widget', 5);
        $this->assertTrue($product->isInStock());
    }
}
