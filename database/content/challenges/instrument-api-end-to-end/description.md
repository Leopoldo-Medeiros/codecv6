## The situation

Your API went down last Tuesday. You found out from a customer tweet.

The postmortem action item landed on you: *"We need to know our error rate and
latency before customers do."* The full observability stack (OpenTelemetry,
dashboards, alerting) is next quarter's budget — today you're building the
minimal in-process version: a metrics collector every request passes through,
tracking the three numbers an on-call engineer asks for first: **how many
requests, how many failed, how slow**.

## Your task

Implement the `MetricsCollector` class:

- `startRequest(): void` — marks the start of a request (record the start time).
- `endRequest(int $statusCode): void` — closes it: add the elapsed time to the
  running total, increment the request counter, and count the request as an
  **error when the status code is 400 or above** (a 404 is a failed request to
  the caller, not just a 500).
- `snapshot(): array` — returns the report:

```php
[
    'total_requests' => 4,      // int
    'total_errors'   => 2,      // int
    'error_rate'     => 50.0,   // float, percentage of requests that errored
    'avg_duration_ms'=> 12.4,   // float, mean request duration in milliseconds
]
```

With zero requests recorded, everything is zero (`0`, `0`, `0.0`, `0.0`) — and
no division-by-zero explosion.

## Examples

| **Recorded requests** | **total_errors** | **error_rate** |
| one 200 | `0` | `0.0` |
| one 500 | `1` | `100.0` |
| 200, 500, 200, 404 | `2` | `50.0` |
| none | `0` | `0.0` |

## Hints

- **Hint 1:** `microtime(true)` gives you a float timestamp in seconds; the difference × 1000 is your duration in milliseconds.
- **Hint 2:** the error threshold is `$statusCode >= 400` — the test that sends a 404 is there to catch the common "only 5xx counts" assumption.
- **Hint 3:** `error_rate` and `avg_duration_ms` must be floats (`assertSame(50.0, …)` fails on the int `50`). Guard the division: compute rates only when `total_requests > 0`.

## In the real world

You just built the **RED method** — Rate, Errors, Duration — the trio every
monitoring vendor's golden dashboard shows first. In production this lives in
middleware (Laravel's terminable middleware is the natural home) and ships to
a time-series backend instead of an in-memory array. The incidents in the
Observability 101 path are diagnosed almost entirely from these three signals;
here you've seen exactly where they come from.
