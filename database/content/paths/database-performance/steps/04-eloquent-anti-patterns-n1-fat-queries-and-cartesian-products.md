---json
{
    "order": 4,
    "title": "Eloquent Anti-Patterns: N+1, Fat Queries, and Cartesian Products",
    "type": "reading",
    "description": "The N+1 problem is well-known, but it is only one of many Eloquent anti-patterns. We will also cover: loading entire relationships when you only need one column (fat queries), Cartesian products from joining without proper constraints, counting via `count($collection)` on already-paginated results, and using `whereIn` with thousands of IDs. Each one looks innocent in development and destroys performance at scale.",
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
            "url": "https://laravel.com/docs/eloquent-relationships#eager-loading",
            "label": "Eloquent Eager Loading"
        },
        {
            "url": "https://laravel.com/docs/queries",
            "label": "Laravel Query Builder"
        },
        {
            "url": "https://github.com/barryvdh/laravel-debugbar",
            "label": "Detecting N+1 with Laravel Debugbar"
        }
    ],
    "instructions": null,
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

