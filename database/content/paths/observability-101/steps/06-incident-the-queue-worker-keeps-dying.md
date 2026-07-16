---json
{
    "order": 6,
    "title": "Incident: the queue worker keeps dying",
    "type": "incident",
    "description": "A worker is OOM-killed on a schedule. Read the shape of its memory over time.",
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
                "A flat line",
                "A sawtooth — climbing to the limit, then dropping at each restart",
                "A one-time spike after a deploy",
                "Random noise"
            ],
            "question": "What pattern does the memory metric show?",
            "explanation": "Memory climbs steadily to the 512MB cap, the worker is OOM-killed and restarts (dropping to ~90MB), then climbs again. That repeating sawtooth is the signature of a leak.",
            "correct_index": 1
        },
        {
            "id": 2,
            "options": [
                "A cache stampede",
                "A memory leak — memory grows unbounded within the worker until it hits the limit",
                "A missing index",
                "A slow external dependency"
            ],
            "question": "What is the root cause?",
            "explanation": "Something accumulates in memory as the worker processes jobs and is never released, so usage only ever grows until the process is killed.",
            "correct_index": 1
        },
        {
            "id": 3,
            "options": [
                "Just raise memory_limit to 2GB",
                "Find and fix what accumulates (unbounded array/cache, uncleared static state, retained references)",
                "Add a database index",
                "Roll back the deploy"
            ],
            "question": "What is the best fix?",
            "explanation": "Raising the limit only delays the crash — the leak still grows. Find what grows per job (a static array never cleared, an in-memory query log, event listeners piling up) and release it. Restarting workers periodically is a stopgap, not a fix.",
            "correct_index": 1
        }
    ],
    "evidence": {
        "logs": [
            {
                "t": "02:28:40",
                "msg": "memory usage 505MB / 512MB",
                "level": "WARN",
                "request_id": "worker_7f3"
            },
            {
                "t": "02:30:02",
                "msg": "worker killed (OOM), restarting",
                "level": "ERROR",
                "request_id": "worker_7f3"
            },
            {
                "t": "02:30:05",
                "msg": "worker restarted, memory 92MB",
                "level": "INFO",
                "request_id": "worker_8a1"
            }
        ],
        "metrics": [
            {
                "unit": "MB",
                "title": "worker memory",
                "series": [
                    [
                        0,
                        120
                    ],
                    [
                        15,
                        300
                    ],
                    [
                        28,
                        505
                    ],
                    [
                        30,
                        92
                    ],
                    [
                        45,
                        310
                    ],
                    [
                        58,
                        508
                    ],
                    [
                        60,
                        95
                    ],
                    [
                        75,
                        320
                    ],
                    [
                        88,
                        506
                    ],
                    [
                        90,
                        98
                    ],
                    [
                        110,
                        360
                    ]
                ],
                "threshold": 512,
                "annotations": [
                    {
                        "x": 30,
                        "label": "OOM restart"
                    },
                    {
                        "x": 60,
                        "label": "OOM restart"
                    },
                    {
                        "x": 90,
                        "label": "OOM restart"
                    }
                ]
            }
        ],
        "scenario": "A queue worker keeps getting killed and restarted roughly every 30 minutes. Jobs complete fine, but throughput dips at each restart. Here is the worker's memory over two hours (limit 512MB)."
    }
}
---

