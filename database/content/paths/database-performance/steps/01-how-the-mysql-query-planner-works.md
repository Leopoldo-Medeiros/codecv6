---json
{
    "order": 1,
    "title": "How the MySQL Query Planner Works",
    "type": "reading",
    "description": "Before MySQL runs a query, the query planner evaluates several execution strategies and picks the cheapest one — based on row estimates, index statistics, and join order. Understanding how it thinks is the foundation of all query optimization. We will cover the concept of cardinality (how selective an index is), how the planner chooses between a full table scan and an index seek, and why its estimates are sometimes wrong — and what to do about it.",
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
            "url": "https://dev.mysql.com/doc/refman/8.0/en/query-optimization.html",
            "label": "MySQL Query Optimization"
        },
        {
            "url": "https://dev.mysql.com/doc/refman/8.0/en/how-mysql-uses-indexes.html",
            "label": "How MySQL Chooses Indexes"
        },
        {
            "url": "https://use-the-index-luke.com/",
            "label": "Use The Index, Luke — free book"
        }
    ],
    "instructions": null,
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

