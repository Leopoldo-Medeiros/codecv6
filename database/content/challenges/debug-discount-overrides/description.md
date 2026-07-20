## The situation

The pricing service keeps per-product discounts **keyed by product ID**:
`[101 => 10, 102 => 15]`. During a flash sale, a smaller overrides map is
applied on top — same product ID, new percentage.

In production, two things went wrong at once: product 101's override
vanished from the result, and — stranger — code elsewhere that looks
discounts up **by product ID** stopped finding anything. The IDs themselves
changed. One innocent-looking built-in call did both.

## Your task

The test is already written and currently **fails**:

```php
$result = applyDiscountOverrides([101 => 10, 102 => 15], [101 => 20]);
// expected: [101 => 20, 102 => 15]
```

Fix `applyDiscountOverrides()` — the fix is choosing a different built-in
function.

## Hints

- **Hint 1:** `var_dump` the current result and look at the *keys*. `array_merge` renumbers **integer** keys from zero — `101` and `102` become `0`, `1`, `2`, and "override by ID" quietly becomes "append to list".
- **Hint 2:** the built-in that merges while *preserving* keys and letting the second array win collisions is `array_replace`. (The `+` operator also preserves keys but resolves collisions the other way — the base would beat the override.)

## In the real world

`array_merge` vs `array_replace` vs `+` is a genuine PHP interview staple
because each handles integer keys and collisions differently — and ID-keyed
maps (product IDs, user IDs, currency codes that happen to be numeric) are
everywhere in real systems. The bug is nastiest with IDs that *look* like
strings but are numeric: PHP silently casts `"101"` to int `101` as an array
key, so `array_merge` renumbers those too. When a keyed lookup mysteriously
returns null after a merge, check the keys before you check the values —
and in Laravel, reach for `collect($base)->replace($overrides)`, which does
the right thing by name.
