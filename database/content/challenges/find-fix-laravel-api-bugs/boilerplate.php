<?php

class Product
{
    public function __construct(
        public int $id,
        public string $name,
        public int $stock,
    ) {}

    public function isInStock(): bool
    {
        // BUG #3: this returns true even when stock is 0
        return $this->stock >= 0;
    }

    public function reduceStock(int $quantity): void
    {
        $this->stock -= $quantity;
    }
}

class Order
{
    private static int $nextId = 1;
    public int $id;
    public array $items;

    public function __construct()
    {
        $this->id = self::$nextId++;
        $this->items = [];
    }

    public function addItem(Product $product, int $quantity): void
    {
        $this->items[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'quantity' => $quantity,
        ];
    }
}

class OrderRepository
{
    private array $orders = [];

    public function save(Order $order): void
    {
        // BUG #1: saves the order twice (race condition simulation)
        $this->orders[] = $order;
        $this->orders[] = $order;
    }

    public function all(): array
    {
        return $this->orders;
    }

    public function count(): int
    {
        return count($this->orders);
    }
}

class UserController
{
    private string $currentRole;
    private int $currentUserId;

    public function __construct(string $role, int $userId)
    {
        $this->currentRole = $role;
        $this->currentUserId = $userId;
    }

    /**
     * Delete a user account.
     * Returns ['success' => true] or ['error' => string].
     */
    public function deleteUser(int $targetUserId): array
    {
        // BUG #2: admin can delete their own account — should be prevented
        if ($this->currentRole === 'admin') {
            return ['success' => true, 'message' => 'User deleted'];
        }

        if ($this->currentUserId === $targetUserId) {
            return ['error' => 'Cannot delete your own account'];
        }

        return ['success' => true, 'message' => 'User deleted'];
    }
}

function checkout(Product $product, int $quantity): array
{
    $order = new Order();
    $order->addItem($product, $quantity);
    $product->reduceStock($quantity);

    $repo = new OrderRepository();
    $repo->save($order);

    return ['order_id' => $order->id, 'items' => count($repo->all())];
}
