---json
{
    "order": 5,
    "title": "Incident: intermittent 500s, but the queries are fast",
    "type": "incident",
    "description": "Some requests fail under load though the SQL itself is quick. Read where the time goes.",
    "tldr": null,
    "difficulty": "advanced",
    "estimated_minutes": 12,
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
                "Executing the SQL query",
                "Waiting to acquire a database connection",
                "AuthMiddleware",
                "Serializing the JSON response"
            ],
            "question": "In the trace, where is the 5.2s going?",
            "explanation": "The query runs in 12ms. The request spends ~5s in the \"waiting for DB connection\" span — the time is in the wait, not the work. That distinction is the whole lesson.",
            "correct_index": 1
        },
        {
            "id": 2,
            "options": [
                "A slow query needing an index",
                "Connection-pool exhaustion — every connection is already in use",
                "An N+1 query",
                "A slow external dependency"
            ],
            "question": "What is the root cause?",
            "explanation": "The connections-in-use metric is pinned at the pool max (20) and the log says \"Too many connections\". New requests block waiting for a connection to free up.",
            "correct_index": 1
        },
        {
            "id": 3,
            "options": [
                "Add an index to the orders table",
                "Raise the pool size and hunt for code holding connections too long (long transactions, leaks)",
                "Increase the request timeout",
                "Add a caching layer"
            ],
            "question": "What is the best fix?",
            "explanation": "A 12ms query needs no index. Give the pool more headroom and — more importantly — find connections held open too long (long-running transactions, connections never released) so they return to the pool quickly.",
            "correct_index": 1
        }
    ],
    "evidence": {
        "logs": [
            {
                "t": "18:20:11",
                "msg": "waited 5000ms to acquire a connection from pool (max=20, in use=20)",
                "level": "WARN",
                "request_id": "req_d90"
            },
            {
                "t": "18:20:16",
                "msg": "SQLSTATE[HY000] [1040]: Too many connections",
                "level": "ERROR",
                "request_id": "req_d91"
            }
        ],
        "trace": {
            "root": "GET /api/orders",
            "spans": [
                {
                    "id": "a",
                    "dur": 5200,
                    "kind": "server",
                    "name": "GET /api/orders",
                    "start": 0,
                    "parent": null,
                    "service": "web"
                },
                {
                    "id": "c",
                    "dur": 5190,
                    "kind": "internal",
                    "name": "OrderController@index",
                    "start": 4,
                    "parent": "a",
                    "service": "web"
                },
                {
                    "id": "w",
                    "dur": 5000,
                    "kind": "db",
                    "name": "waiting for DB connection (pool)",
                    "start": 10,
                    "parent": "c",
                    "service": "db"
                },
                {
                    "id": "q",
                    "dur": 12,
                    "kind": "db",
                    "name": "SELECT orders WHERE user_id=?",
                    "start": 5015,
                    "parent": "c",
                    "service": "db"
                }
            ]
        },
        "metrics": [
            {
                "unit": "/20",
                "title": "DB connections in use",
                "series": [
                    [
                        0,
                        8
                    ],
                    [
                        5,
                        14
                    ],
                    [
                        10,
                        20
                    ],
                    [
                        15,
                        20
                    ],
                    [
                        20,
                        20
                    ],
                    [
                        25,
                        20
                    ]
                ],
                "threshold": 20
            }
        ],
        "scenario": "Under peak traffic, some requests return 500s and others hang — yet the SQL queries themselves are fast. Here is a trace of one slow request, plus the DB connection metric."
    }
}
---

