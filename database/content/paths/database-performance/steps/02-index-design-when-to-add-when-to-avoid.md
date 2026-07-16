---json
{
    "order": 2,
    "title": "Index Design: When to Add, When to Avoid",
    "type": "reading",
    "description": "Indexes are not free — every write operation must update every index on the table. Adding an index to every column is not a solution; it is a different problem. We will cover composite indexes and the left-prefix rule, covering indexes that eliminate table lookups, partial indexes for sparse data, and the specific conditions under which an index will be silently ignored by the planner (functions on indexed columns, implicit type casts, leading wildcards in LIKE).",
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
            "url": "https://dev.mysql.com/doc/refman/8.0/en/optimization-indexes.html",
            "label": "MySQL Index Types"
        },
        {
            "url": "https://use-the-index-luke.com/sql/where-clause/the-equals-operator/concatenated-keys",
            "label": "Composite Index Strategy"
        },
        {
            "url": "https://laravel.com/docs/migrations#available-index-types",
            "label": "Laravel migration index methods"
        }
    ],
    "instructions": null,
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

