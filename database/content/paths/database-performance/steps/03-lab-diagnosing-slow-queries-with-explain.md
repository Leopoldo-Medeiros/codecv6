---json
{
    "order": 3,
    "title": "Lab: Diagnosing Slow Queries with EXPLAIN",
    "type": "lab",
    "description": "`EXPLAIN` is the single most important tool in database optimization — it shows you exactly what the planner decided to do with your query. `EXPLAIN ANALYZE` runs the query and shows what it actually did versus what it expected. We will use both to identify full table scans, poor index selection, and unexpected filesorts on a real Laravel application.",
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
            "url": "https://dev.mysql.com/doc/refman/8.0/en/explain-output.html",
            "label": "MySQL EXPLAIN output format"
        },
        {
            "url": "https://dev.mysql.com/doc/refman/8.0/en/explain.html",
            "label": "EXPLAIN ANALYZE"
        },
        {
            "url": "https://laravel.com/docs/telescope",
            "label": "Laravel Telescope — query monitoring"
        }
    ],
    "instructions": [
        {
            "id": 1,
            "text": "Enable the slow query log: set `slow_query_log = 1` and `long_query_time = 0.1` in MySQL config"
        },
        {
            "id": 2,
            "text": "Run a test endpoint that you suspect is slow — generate at least 100 requests with a load tool"
        },
        {
            "id": 3,
            "text": "Identify the slowest query in the slow query log and copy it"
        },
        {
            "id": 4,
            "text": "Run `EXPLAIN FORMAT=JSON <your query>` and read the output — note the `type`, `rows`, and `key` fields"
        },
        {
            "id": 5,
            "text": "If type is \"ALL\" (full scan), identify which column is used in the WHERE clause and is missing an index"
        },
        {
            "id": 6,
            "text": "Add the index via a Laravel migration and run EXPLAIN again — confirm `type` changed to `ref` or `range`"
        },
        {
            "id": 7,
            "text": "Measure: re-run the load test and compare the p95 latency before and after"
        }
    ],
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

