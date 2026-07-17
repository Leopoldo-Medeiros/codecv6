<?php

use PHPUnit\Framework\TestCase;

class ArrayPipelineTest extends TestCase
{
    private array $catalogue = [
        ['name' => 'Widget B', 'price_cents' => 15000, 'active' => true],
        ['name' => 'Widget A', 'price_cents' => 3000,  'active' => true],
        ['name' => 'Widget C', 'price_cents' => 20000, 'active' => false],
        ['name' => 'Widget D', 'price_cents' => 30000, 'active' => true],
        ['name' => 'Widget E', 'price_cents' => 5000,  'active' => true],
    ];

    public function test_returns_only_active_products_above_threshold(): void
    {
        $result = formatPremiumProducts($this->catalogue);
        $this->assertCount(2, $result);
    }

    public function test_formats_price_as_euros_with_two_decimals(): void
    {
        $result = formatPremiumProducts($this->catalogue);
        $this->assertContains('Widget B — €150.00', $result);
        $this->assertContains('Widget D — €300.00', $result);
    }

    public function test_result_is_sorted_alphabetically(): void
    {
        $result = formatPremiumProducts($this->catalogue);
        $this->assertSame(['Widget B — €150.00', 'Widget D — €300.00'], $result);
    }

    public function test_filters_out_inactive_products(): void
    {
        $result = formatPremiumProducts($this->catalogue);
        foreach ($result as $label) {
            $this->assertStringNotContainsString('Widget C', $label);
        }
    }

    public function test_filters_out_products_at_threshold(): void
    {
        $result = formatPremiumProducts($this->catalogue);
        foreach ($result as $label) {
            $this->assertStringNotContainsString('Widget E', $label);
        }
    }

    public function test_returns_empty_array_for_empty_input(): void
    {
        $this->assertSame([], formatPremiumProducts([]));
    }
}
