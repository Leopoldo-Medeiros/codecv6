---json
{
    "order": 2,
    "title": "Incident: checkout is slow, but the database looks fine",
    "type": "incident",
    "description": "The slowness is real, but it is not in your code. Find where it lives.",
    "tldr": null,
    "difficulty": "intermediate",
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
                "The database queries",
                "The payments-gateway calls, including retries",
                "Auth and routing",
                "JSON serialization"
            ],
            "question": "Where is the request time going?",
            "explanation": "The DB span is 14ms. The three payments-gateway attempts (two timing out, one succeeding) consume ~3.15s of the 3.26s request.",
            "correct_index": 1
        },
        {
            "id": 2,
            "options": [
                "An N+1 query",
                "A missing index",
                "A slow / failing external dependency",
                "A memory leak"
            ],
            "question": "What is the root cause?",
            "explanation": "Your DB and code are fine — the payments provider is timing out, and your client retries three times. The problem lives in someone else's system.",
            "correct_index": 2
        },
        {
            "id": 3,
            "options": [
                "Add a database index",
                "Add a short timeout + circuit breaker so you fail fast and degrade gracefully",
                "Increase PHP memory_limit",
                "Eager-load the products"
            ],
            "question": "What is the best response? You cannot make their service faster.",
            "explanation": "You cannot fix a third party's service. You protect YOUR service: a tight timeout, a circuit breaker to stop hammering a failing dependency, and a graceful fallback so one slow provider does not take checkout down.",
            "correct_index": 1
        }
    ],
    "evidence": {
        "logs": [
            {
                "t": "15:41:10",
                "msg": "payments-gateway timeout after 1000ms — retrying (2/3)",
                "level": "WARN",
                "request_id": "req_c71"
            },
            {
                "t": "15:41:11",
                "msg": "payments-gateway timeout after 1000ms — retrying (3/3)",
                "level": "WARN",
                "request_id": "req_c71"
            },
            {
                "t": "15:41:12",
                "msg": "payments-gateway ok on attempt 3 (2050ms spent on retries)",
                "level": "INFO",
                "request_id": "req_c71"
            }
        ],
        "trace": {
            "root": "POST /checkout",
            "spans": [
                {
                    "id": "a",
                    "dur": 3260,
                    "kind": "server",
                    "name": "POST /checkout",
                    "start": 0,
                    "parent": null,
                    "service": "web"
                },
                {
                    "id": "c",
                    "dur": 3250,
                    "kind": "internal",
                    "name": "CartController@checkout",
                    "start": 6,
                    "parent": "a",
                    "service": "web"
                },
                {
                    "id": "d",
                    "dur": 14,
                    "kind": "db",
                    "name": "SELECT cart + items",
                    "start": 10,
                    "parent": "c",
                    "service": "db"
                },
                {
                    "id": "r1",
                    "dur": 1000,
                    "kind": "client",
                    "name": "POST payments-gateway (try 1)",
                    "start": 30,
                    "parent": "c",
                    "service": "ext"
                },
                {
                    "id": "r2",
                    "dur": 1000,
                    "kind": "client",
                    "name": "POST payments-gateway (try 2)",
                    "start": 1035,
                    "parent": "c",
                    "service": "ext"
                },
                {
                    "id": "r3",
                    "dur": 1150,
                    "kind": "client",
                    "name": "POST payments-gateway (try 3)",
                    "start": 2040,
                    "parent": "c",
                    "service": "ext"
                }
            ]
        },
        "metrics": [
            {
                "unit": "ms",
                "title": "payments-gateway p95",
                "series": [
                    [
                        0,
                        240
                    ],
                    [
                        5,
                        260
                    ],
                    [
                        10,
                        1900
                    ],
                    [
                        15,
                        3050
                    ],
                    [
                        20,
                        3200
                    ]
                ],
                "threshold": 800
            }
        ],
        "scenario": "Checkout is slow again — but this time the database spans are tiny and your code has not changed. The slowdown began right after your payments provider posted a status-page incident. Here is a trace."
    }
}
---

