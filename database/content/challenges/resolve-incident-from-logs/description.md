## The situation

02:14. PagerDuty wakes you: the API is throwing errors. The deploy pipeline is
locked, the APM trial expired last month, and the engineer who knows this
service is on a plane. What you have is `kubectl logs` — a stream of lines
like:

```
[2026-05-06 12:00:01] [ERROR] [/api/users] Database connection timeout
[2026-05-06 12:00:02] [ERROR] [/api/orders] Database connection timeout
[2026-05-06 12:00:04] [INFO] [/api/health] Health check passed
```

Reading logs under pressure is a skill; **turning them into a structured
incident report** is the senior version of that skill. You're writing the
analyzer you wish you had at 02:14.

## Your task

Implement `analyzeLogs(array $logLines): array`. Each line follows
`[TIMESTAMP] [LEVEL] [ENDPOINT] message`. Return:

```php
[
    'severity'           => 'critical',              // see rules below
    'root_cause'         => 'database_connection',   // see rules below
    'affected_endpoints' => ['/api/users', ...],     // unique, ERROR lines only
    'error_count'        => 3,                       // number of ERROR lines
]
```

The classification rules:

- `error_count` — how many lines have level `ERROR`. Warnings and info lines
  are context, not errors.
- `affected_endpoints` — the unique endpoints that appear in **ERROR** lines.
  An endpoint that only logged INFO is healthy; don't page its owner.
- `root_cause` — `'database_connection'` when the error messages point at
  database connection trouble (timeouts, refused connections).
- `severity` — `'low'` when there are no errors (including empty input and
  warning-only logs); `'critical'` when errors pile up (10 or more).

## Examples

| **Input** | **severity** | **error_count** |
| 3 DB-timeout ERRORs + 1 INFO | elevated | `3` |
| 10 identical DB-timeout ERRORs | `critical` | `10` |
| 1 WARNING + 1 INFO | `low` | `0` |
| `[]` | `low` | `0` |

## Hints

- **Hint 1:** don't reach for a monster regex — `[LEVEL]` and `[ENDPOINT]` are the 2nd and 3rd bracketed fields; a targeted pattern like `/\[([A-Z]+)\] \[([^\]]+)\]/` gets both in one match.
- **Hint 2:** collect endpoints in an array and deduplicate with `array_unique` + `array_values` — the tests use `assertContains`/`assertNotContains`, so order is free but duplicates are not the point.
- **Hint 3:** `str_contains($message, 'Database connection')` is enough for the root-cause check — real log classifiers start life exactly this naive.

## In the real world

This is what log aggregators (Loki, CloudWatch Insights, Splunk) do at scale:
parse structure out of text, group by level and route, and rank probable
causes by frequency. It's also why structured logging (JSON lines with
explicit `level` and `endpoint` fields) beats free-text — you'll appreciate
that the moment your regex meets a log line someone wrote by hand. The
Observability 101 path has a full incident (`LogStream` evidence) built on
this exact skill.
