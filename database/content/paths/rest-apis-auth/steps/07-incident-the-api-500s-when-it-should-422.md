---json
{
    "order": 7,
    "title": "Incident: the API 500s when it should 422",
    "type": "incident",
    "description": "A create-order endpoint crashes on bad input. Read the telemetry and find why — and why it is the wrong status code.",
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
                "The auth token",
                "The \"quantity\" field",
                "The \"product_id\" field",
                "Nothing — the payload is fine"
            ],
            "question": "Looking at the logged payload, what did the client leave out?",
            "explanation": "The payload is {product_id, currency} — no quantity. The code then does qty × price with qty = null.",
            "correct_index": 1
        },
        {
            "id": 2,
            "options": [
                "The database was down",
                "The controller used the input without validating it first, so a TypeError bubbled up",
                "The route was misconfigured",
                "Rate limiting kicked in"
            ],
            "question": "Why did the API return 500 instead of 422?",
            "explanation": "No validation ran, so null reached computeTotal() and threw a TypeError — an unhandled 500. With validation, the missing field is a clean 422.",
            "correct_index": 1
        },
        {
            "id": 3,
            "options": [
                "Wrap store() in try/catch and return 500 more nicely",
                "Validate the request (FormRequest) so a missing field returns 422 with a field error",
                "Increase PHP memory_limit",
                "Make the client retry automatically"
            ],
            "question": "What is the correct fix?",
            "explanation": "Validate at the edge. A missing/invalid field should be a 422 with { errors: { quantity: [...] } }, telling the client exactly what to fix — never a 500.",
            "correct_index": 1
        }
    ],
    "evidence": {
        "logs": [
            {
                "t": "14:22:07",
                "msg": "POST /orders payload={\"product_id\":5,\"currency\":\"eur\"}",
                "level": "INFO",
                "request_id": "req_o12"
            },
            {
                "t": "14:22:07",
                "msg": "TypeError: Unsupported operand types: int * null — OrderController::store():34",
                "level": "ERROR",
                "request_id": "req_o12"
            },
            {
                "t": "14:22:07",
                "msg": "response 500 Internal Server Error",
                "level": "INFO",
                "request_id": "req_o12"
            }
        ],
        "trace": {
            "root": "POST /orders",
            "spans": [
                {
                    "id": "a",
                    "dur": 210,
                    "kind": "server",
                    "name": "POST /orders",
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
                    "dur": 198,
                    "kind": "internal",
                    "name": "OrderController@store",
                    "start": 8,
                    "parent": "a",
                    "service": "web"
                },
                {
                    "id": "d",
                    "dur": 2,
                    "kind": "internal",
                    "name": "computeTotal(qty × price)",
                    "start": 30,
                    "parent": "c",
                    "service": "web"
                }
            ]
        },
        "scenario": "Users report the \"Place order\" button sometimes shows a generic \"Server error\". Your monitoring shows a spike of 500s on POST /orders — but only for some requests. Here is a trace and the logs for one failing request."
    }
}
---

