## Readonly Money Value Object

PHP 8.1 **readonly properties** enforce immutability at the language level. Combined with constructor promotion, they make Value Objects concise and safe — mutation returns a new instance rather than modifying the original.

**Goal:** implement three methods on the `Money` class:
- `add(Money $other): Money` — returns a **new** `Money` with the combined amounts. Throw `\InvalidArgumentException` if the currencies differ.
- `toFloat(): float` — returns `amount / 100` (cents to major units).
- `format(): string` — returns `"€12.50"` for `EUR` and `"R$12.50"` for `BRL`.

```php
$a = new Money(1000, 'EUR'); // €10.00
$b = new Money(500,  'EUR'); // €5.00
$c = $a->add($b);            // new Money(1500, 'EUR')
// $a and $b are unchanged — readonly enforces this
```
