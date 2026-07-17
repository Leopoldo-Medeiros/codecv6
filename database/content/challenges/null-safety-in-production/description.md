## Null Safety in Production

In observability platforms, a **transaction** carries an optional **span** that holds custom attributes. Reading a missing attribute with a direct property access causes a fatal error — crashing your monitoring instrumentation at the worst possible time.

**Goal:** rewrite `getCustomAttributeValue()` so it safely returns `null` when either the transaction has no active span, or the span does not carry the requested attribute.

Use the **null-safe operator** `?->` to short-circuit the chain without conditionals.

```php
// Before — crashes with a fatal error
return $transaction->currentSpan->getAttribute($name)->value;

// After — returns null safely
return $transaction->currentSpan?->getAttribute($name)?->value;
```

**Why it matters:** in production, spans are created and destroyed across async boundaries. Assuming a span always exists is a guaranteed outage waiting to happen.
