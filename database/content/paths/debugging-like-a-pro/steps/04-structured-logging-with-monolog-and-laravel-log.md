---json
{
    "order": 4,
    "title": "Structured Logging with Monolog and Laravel Log",
    "type": "lab",
    "description": "Logs are your application's memory in production — where Xdebug does not exist. Structured logs (JSON) are searchable, filterable and integrate with any observability stack. We will configure separate channels by context, correct log levels, and context data that makes each log entry self-sufficient for investigation.",
    "tldr": null,
    "difficulty": null,
    "estimated_minutes": null,
    "challenge_slug": null,
    "lab_url": null,
    "has_playground": false,
    "playground_starter_code": null,
    "challenge_prompt": null,
    "resources": [
        {
            "url": "https://laravel.com/docs/logging",
            "label": "Laravel Logging"
        },
        {
            "url": "https://seldaek.github.io/monolog/",
            "label": "Monolog documentation"
        },
        {
            "url": "https://www.loggly.com/use-cases/best-practices-for-php-logging/",
            "label": "Structured Logging best practices"
        }
    ],
    "instructions": [
        {
            "id": 1,
            "text": "Configure a `structured` channel in config/logging.php with JSON formatter"
        },
        {
            "id": 2,
            "text": "Add global context via `Log::withContext()` in middleware: user_id, request_id, ip"
        },
        {
            "id": 3,
            "text": "Implement correct log levels: DEBUG for dev, INFO for user actions, ERROR for failures"
        },
        {
            "id": 4,
            "text": "Create a custom Processor that adds `memory_usage` and `duration_ms` to each log"
        },
        {
            "id": 5,
            "text": "Configure log rotation: daily logs with 30-day retention"
        },
        {
            "id": 6,
            "text": "Simulate a production bug and resolve it using only the generated logs"
        }
    ],
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

