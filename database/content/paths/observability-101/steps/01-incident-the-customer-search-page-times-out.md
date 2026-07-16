---json
{
    "order": 1,
    "title": "Incident: the customer search page times out",
    "type": "incident",
    "description": "One endpoint is slow while everything else is fast. Find out why.",
    "tldr": null,
    "difficulty": "beginner",
    "estimated_minutes": 10,
    "challenge_slug": null,
    "lab_url": null,
    "has_playground": false,
    "playground_starter_code": null,
    "challenge_prompt": null,
    "resources": null,
    "instructions": null,
    "prerequisites": null,
    "concepts": null,
    "quiz": [
        {
            "id": 1,
            "options": [
                "AuthMiddleware",
                "The SELECT customers query",
                "HTTP request parsing",
                "JSON serialization"
            ],
            "question": "Which operation dominates the request time?",
            "explanation": "A single span — the customers query — takes 2075ms of the 2100ms request. Everything else is negligible.",
            "correct_index": 1
        },
        {
            "id": 2,
            "options": [
                "An N+1 query",
                "A missing index — the query does a full table scan",
                "A slow external dependency",
                "A memory leak"
            ],
            "question": "What is the root cause? Note there is exactly ONE slow query, not many.",
            "explanation": "Unlike an N+1 (many fast queries), this is ONE query scanning 482k rows (type=ALL) because customers.email has no index. Reading the trace shape — one huge span vs. many small ones — is how you tell them apart.",
            "correct_index": 1
        },
        {
            "id": 3,
            "options": [
                "Eager-load a relation",
                "Add an index on customers.email",
                "Add a Redis cache in front of the query",
                "Increase PHP memory_limit"
            ],
            "question": "Which fix resolves it?",
            "explanation": "An index on customers.email turns the full scan into an index lookup — milliseconds instead of seconds. A cache would only hide the problem for repeated searches.",
            "correct_index": 1
        }
    ],
    "evidence": {
        "logs": [
            {
                "t": "09:11:02",
                "msg": "customer search email=jane@acme.io",
                "level": "INFO",
                "request_id": "req_a45"
            },
            {
                "t": "09:11:04",
                "msg": "slow query 2075ms — full table scan (type=ALL, rows_examined=482113, no index on customers.email)",
                "level": "WARN",
                "request_id": "req_a45"
            }
        ],
        "trace": {
            "root": "GET /admin/customers/search",
            "spans": [
                {
                    "id": "a",
                    "dur": 2100,
                    "kind": "server",
                    "name": "GET /admin/customers/search",
                    "start": 0,
                    "parent": null,
                    "service": "web"
                },
                {
                    "id": "b",
                    "dur": 4,
                    "kind": "internal",
                    "name": "AuthMiddleware",
                    "start": 2,
                    "parent": "a",
                    "service": "web"
                },
                {
                    "id": "c",
                    "dur": 2090,
                    "kind": "internal",
                    "name": "CustomerController@search",
                    "start": 8,
                    "parent": "a",
                    "service": "web"
                },
                {
                    "id": "q",
                    "dur": 2075,
                    "kind": "db",
                    "name": "SELECT * FROM customers WHERE email=?",
                    "start": 14,
                    "parent": "c",
                    "service": "db"
                }
            ]
        },
        "metrics": [
            {
                "unit": "ms",
                "title": "customer search p95",
                "series": [
                    [
                        0,
                        180
                    ],
                    [
                        5,
                        600
                    ],
                    [
                        10,
                        1200
                    ],
                    [
                        15,
                        1800
                    ],
                    [
                        20,
                        2100
                    ]
                ],
                "threshold": 500
            }
        ],
        "scenario": "The admin \"find customer by email\" page times out, but every other page is fast. It started after the customers table grew past ~half a million rows. Here is a trace of one search request."
    }
}
---

