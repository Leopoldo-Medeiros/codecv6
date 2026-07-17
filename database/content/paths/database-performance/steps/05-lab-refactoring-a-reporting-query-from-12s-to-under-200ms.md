---json
{
    "order": 5,
    "title": "Lab: Refactoring a Reporting Query from 12s to under 200ms",
    "type": "lab",
    "description": "A real-world reporting query with multiple JOINs, aggregates, and no indexes. We will work through the optimization process methodically: read the query plan, identify the bottleneck at each step, add indexes and restructure the query, and measure the improvement. By the end, you will have a repeatable process for attacking any slow query.",
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
            "url": "https://laravel.com/docs/database#running-queries",
            "label": "Laravel DB::select and raw queries"
        },
        {
            "url": "https://laravel.com/docs/cache",
            "label": "Query caching strategies"
        },
        {
            "url": "https://use-the-index-luke.com/sql/where-clause",
            "label": "Database indexing strategy guide"
        }
    ],
    "instructions": [
        {
            "id": 1,
            "text": "Open the provided reporting endpoint and run it against a seeded dataset of 100k rows — note the response time"
        },
        {
            "id": 2,
            "text": "Enable `DB::listen()` temporarily to capture every query executed during the request"
        },
        {
            "id": 3,
            "text": "Run `EXPLAIN` on the slowest query — identify the full scan"
        },
        {
            "id": 4,
            "text": "Add a composite index covering the WHERE and ORDER BY columns: `$table->index(['user_id', 'created_at'])`"
        },
        {
            "id": 5,
            "text": "Rewrite any N+1 found with `with()` or `withCount()`"
        },
        {
            "id": 6,
            "text": "Move expensive aggregates to a database view or a dedicated summary table updated via a scheduled job"
        },
        {
            "id": 7,
            "text": "Add a Redis cache layer with a 5-minute TTL for data that does not need to be real-time"
        }
    ],
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

