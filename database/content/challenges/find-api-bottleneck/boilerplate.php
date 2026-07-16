<?php

class Category
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}

class Product
{
    public function __construct(
        public int $id,
        public string $name,
        public int $categoryId,
        public ?Category $category = null,
    ) {}
}

function getCategoryById(int $id): Category
{
    static $categories = [
        1 => new Category(1, 'Electronics'),
        2 => new Category(2, 'Books'),
        3 => new Category(3, 'Clothing'),
    ];
    return $categories[$id] ?? new Category($id, 'Unknown');
}

function getAllProducts(): array
{
    return [
        new Product(1, 'Laptop', 1),
        new Product(2, 'Phone', 1),
        new Product(3, 'PHP Book', 2),
        new Product(4, 'T-Shirt', 3),
    ];
}

function getProductsWithCategories(): array
{
    $products = getAllProducts();
    $result = [];

    foreach ($products as $product) {
        // BUG: N+1 query — fetches category for each product individually
        $category = getCategoryById($product->categoryId);
        $result[] = [
            'id' => $product->id,
            'name' => $product->name,
            'category' => $category->name,
        ];
    }

    return $result;
}

function getQueryCount(): int
{
    static $count = 0;
    return $count;
}

function resetQueryCount(): void {}
