---json
{
    "order": 3,
    "title": "Incident: error rate spiked out of nowhere",
    "type": "incident",
    "description": "No trace needed. Correlate the metric with the timeline and read the logs.",
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
                "A traffic spike",
                "A deploy went out (release v2.4.1)",
                "The database went down",
                "The cache expired"
            ],
            "question": "What changed at 14:32?",
            "explanation": "The error rate steps up exactly at the deploy annotation, and the log shows \"release v2.4.1 deployed\" at 14:32:04. That alignment is the clue.",
            "correct_index": 1
        },
        {
            "id": 2,
            "options": [
                "An N+1 query",
                "A code regression introduced by the new release",
                "A slow external dependency",
                "A missing index"
            ],
            "question": "What is the most likely root cause?",
            "explanation": "The errors are a brand-new null-reference in OrderController@store that appears only after v2.4.1 — a regression shipped in that release.",
            "correct_index": 1
        },
        {
            "id": 3,
            "options": [
                "Add a database index",
                "Roll back the release",
                "Increase PHP memory_limit",
                "Add more web replicas"
            ],
            "question": "What is the fastest mitigation?",
            "explanation": "With a bad deploy you roll back first to stop the bleeding, then debug the regression offline. Capacity or indexes cannot fix a code bug.",
            "correct_index": 1
        }
    ],
    "evidence": {
        "logs": [
            {
                "t": "14:32:04",
                "msg": "release v2.4.1 deployed (git 7c1a9e2)",
                "level": "INFO",
                "request_id": "deploy"
            },
            {
                "t": "14:32:41",
                "msg": "TypeError: Cannot read property 'id' of null in OrderController@store:47",
                "level": "ERROR",
                "request_id": "req_e18"
            },
            {
                "t": "14:33:02",
                "msg": "TypeError: Cannot read property 'id' of null in OrderController@store:47",
                "level": "ERROR",
                "request_id": "req_e2a"
            }
        ],
        "metrics": [
            {
                "unit": "%",
                "title": "5xx error rate",
                "series": [
                    [
                        20,
                        0.1
                    ],
                    [
                        26,
                        0.1
                    ],
                    [
                        30,
                        0.2
                    ],
                    [
                        32,
                        0.3
                    ],
                    [
                        34,
                        7.9
                    ],
                    [
                        40,
                        8.2
                    ],
                    [
                        46,
                        8
                    ]
                ],
                "threshold": 1,
                "annotations": [
                    {
                        "x": 32,
                        "label": "deploy v2.4.1"
                    }
                ]
            }
        ],
        "scenario": "The API 5xx error rate jumped from ~0.1% to 8% at 14:32. Traffic is normal, the database is healthy — but a deploy went out at 14:32. Use the metric and the logs to work out what happened and what to do first."
    }
}
---

