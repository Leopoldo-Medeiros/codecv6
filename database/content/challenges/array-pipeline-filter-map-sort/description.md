## Array Pipeline: Filter, Map, Sort

Functional array operations (`array_filter`, `array_map`, `usort`) are the backbone of data transformation in PHP. Chaining them replaces loops with readable, composable expressions.

**Goal:** implement `formatPremiumProducts()` that:
1. **Filters** out inactive products and products with `price_cents` ≤ 5000 (i.e. ≤ €50.00)
2. **Maps** each remaining product to the string `"Name — €X.XX"` (two decimal places)
3. **Sorts** the result alphabetically by product name

```php
// Example input:
$products = [
    ['name' => 'Widget B', 'price_cents' => 15000, 'active' => true],
    ['name' => 'Widget A', 'price_cents' => 3000,  'active' => true],   // too cheap
    ['name' => 'Widget C', 'price_cents' => 20000, 'active' => false],  // inactive
    ['name' => 'Widget D', 'price_cents' => 30000, 'active' => true],
];
// Expected: ['Widget B — €150.00', 'Widget D — €300.00']
```
