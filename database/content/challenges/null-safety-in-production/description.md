## The situation

Your team maintains the tracing instrumentation inside a monitoring agent.
A **transaction** may carry a current **span**, and a span holds custom
attributes (`user_id`, `route`, …). Last night the agent itself took the
application down: a background job ran *outside* any span, and reading an
attribute hit a fatal error — **the monitoring crashed the thing it was
monitoring**, the most embarrassing outage genre there is.

The offending line assumes the whole chain always exists:

```php
return $transaction->currentSpan->getAttribute($name)->value;
```

In production, spans are created and destroyed across async boundaries.
`currentSpan` can be `null` between requests; `getAttribute()` returns
`null` for attributes never set. Two nullable links, zero guards.

## Your task

Rewrite `getCustomAttributeValue()` so it returns the attribute's value —
or `null` when the transaction has no active span **or** the span doesn't
carry the requested attribute. The null-safe operator `?->` does it without
a single `if`.

## Examples

| **State** | **Result** |
| span with `user_id => 42` | `42` |
| `currentSpan` is `null` | `null` |
| span exists, attribute missing | `null` |
| asked for `region`, span only has `host` | `null` |

## Hints

- **Hint 1:** two of the three arrows need the `?`: the span may be null, and `getAttribute()`'s return may be null. Map each `?->` to the test it saves.
- **Hint 2:** `?->` short-circuits the *whole rest of the chain* — once a link is null, nothing after it executes, arguments included. That's why it's safe even though `->value` comes after a method call.
- **Hint 3:** instrumentation code has a special rule: **it must never throw**. Business code sometimes *wants* the exception (a missing relation may be a bug); an agent that can crash its host is worse than no agent.

## In the real world

This is a real pattern from real tracing SDKs — OpenTelemetry's PHP API
returns no-op spans instead of null for the same reason: telemetry must
degrade, never detonate. You'll meet the identical chain shape in everyday
Laravel (`auth()->user()?->profile?->avatar_url`) and in this platform's own
Observability 101 track, where missing spans in a trace waterfall are a
*diagnostic clue* — here you learned why the code reading them has to
survive their absence. The judgment call that transfers: `?->` where absence
is a legal state; a plain `->` (and a loud exception) where absence means a
bug you want to hear about.
