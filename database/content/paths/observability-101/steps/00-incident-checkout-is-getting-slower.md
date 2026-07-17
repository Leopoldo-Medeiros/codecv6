---json
{
    "order": 0,
    "title": "Incident: checkout is getting slower",
    "type": "incident",
    "description": "Read the telemetry and find why checkout latency is climbing.",
    "tldr": null,
    "difficulty": "intermediate",
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
                "The external payment-gateway call",
                "The repeated \"SELECT products\" lookups",
                "The AuthMiddleware",
                "The initial cart fetch"
            ],
            "question": "Looking at the trace, which operation is responsible for most of the 1.4s?",
            "explanation": "The product lookup runs 120 times at ~8ms each (~960ms total) — far more than the 180ms payment call, even though a single payment span \"looks\" slow at a glance.",
            "correct_index": 1
        },
        {
            "id": 2,
            "options": [
                "A missing database index",
                "An N+1 query — one query per cart item",
                "A slow external dependency",
                "A memory leak in the worker"
            ],
            "question": "What is the root cause?",
            "explanation": "One \"SELECT products WHERE id=?\" fires per cart item (120 items → 120 queries). The log even flags it. That is the classic N+1.",
            "correct_index": 1
        },
        {
            "id": 3,
            "options": [
                "Increase PHP memory_limit",
                "Add a read replica for the database",
                "Eager-load the products (e.g. with('product')) so they load in one query",
                "Raise the checkout timeout"
            ],
            "question": "Which fix resolves it?",
            "explanation": "Eager-loading collapses the 120 per-item queries into a single \"WHERE id IN (...)\" query. The other options mask symptoms without removing the N+1.",
            "correct_index": 2
        }
    ],
    "evidence": {
        "logs": [
            {
                "t": "12:04:31",
                "msg": "checkout started cart_id=8814 items=120",
                "level": "INFO",
                "request_id": "req_9f2"
            },
            {
                "t": "12:04:32",
                "msg": "N+1 detected: SELECT products executed 120 times in a single request",
                "level": "WARN",
                "request_id": "req_9f2"
            },
            {
                "t": "12:04:32",
                "msg": "checkout completed in 1402ms status=200",
                "level": "INFO",
                "request_id": "req_9f2"
            }
        ],
        "trace": {
            "root": "POST /checkout",
            "spans": [
                {
                    "id": "a",
                    "dur": 1400,
                    "kind": "server",
                    "name": "POST /checkout",
                    "start": 0,
                    "parent": null,
                    "service": "web"
                },
                {
                    "id": "b",
                    "dur": 5,
                    "kind": "internal",
                    "name": "AuthMiddleware",
                    "start": 2,
                    "parent": "a",
                    "service": "web"
                },
                {
                    "id": "c",
                    "dur": 1385,
                    "kind": "internal",
                    "name": "CartController@checkout",
                    "start": 8,
                    "parent": "a",
                    "service": "web"
                },
                {
                    "id": "d",
                    "dur": 8,
                    "kind": "db",
                    "name": "SELECT carts WHERE id=?",
                    "start": 10,
                    "parent": "c",
                    "service": "db"
                },
                {
                    "id": "e",
                    "dur": 6,
                    "kind": "db",
                    "name": "SELECT cart_items WHERE cart_id=?",
                    "start": 20,
                    "parent": "c",
                    "service": "db"
                },
                {
                    "id": "n1",
                    "dur": 8,
                    "kind": "db",
                    "name": "SELECT products WHERE id=?",
                    "start": 30,
                    "parent": "c",
                    "repeat": 120,
                    "service": "db"
                },
                {
                    "id": "p",
                    "dur": 180,
                    "kind": "client",
                    "name": "POST payments-gateway",
                    "start": 1210,
                    "parent": "c",
                    "service": "ext"
                }
            ]
        },
        "metrics": [
            {
                "unit": "ms",
                "title": "checkout p95 latency",
                "series": [
                    [
                        0,
                        210
                    ],
                    [
                        4,
                        250
                    ],
                    [
                        8,
                        430
                    ],
                    [
                        12,
                        720
                    ],
                    [
                        16,
                        980
                    ],
                    [
                        20,
                        1400
                    ]
                ],
                "threshold": 500
            }
        ],
        "scenario": "Checkout p95 latency has climbed from ~200ms to 1.4s over the last 20 minutes. No deploy went out. Customers are complaining that \"the buy button spins.\" Here is the telemetry for one slow request — read it and find the cause."
    }
}
---

