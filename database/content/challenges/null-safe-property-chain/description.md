## Null-Safe Property Chain

PHP 8.0 introduced the **null-safe operator** `?->` to short-circuit a chain when any member is `null` instead of throwing an error.

**Goal:** implement `getCity()` so it returns the city name from a nested object graph — or `null` if any link in the chain is absent. Use a single expression with `?->`.

```php
$user?->address?->city?->name
```
