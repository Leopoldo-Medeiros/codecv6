---json
{
    "order": 4,
    "title": "Incident: the database spikes like clockwork",
    "type": "incident",
    "description": "A periodic DB spike lines up with a cache expiry. Read the two metrics together.",
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
                "Only the database is slow",
                "Cache hit rate collapses AND DB CPU spikes at the same instant",
                "Only the cache is permanently broken",
                "A deploy went out"
            ],
            "question": "What do the two metrics show happening together at t=5?",
            "explanation": "The two metrics move inversely at the exact same moment: the moment the cache stops serving (hit rate → ~15%), the DB has to serve everything (CPU → ~99%). Reading correlated metrics together is the skill here.",
            "correct_index": 1
        },
        {
            "id": 2,
            "options": [
                "A missing index",
                "A cache stampede — many requests rebuild the same expired key at once",
                "An N+1 query",
                "A memory leak"
            ],
            "question": "What is the root cause?",
            "explanation": "When the hot key expires, ~1,800 concurrent requests all miss and rebuild it simultaneously (a thundering herd), hammering the DB until one repopulates the cache.",
            "correct_index": 1
        },
        {
            "id": 3,
            "options": [
                "Increase PHP memory_limit",
                "Lock the rebuild so only one request recomputes the key (or serve stale while revalidating), and jitter TTLs",
                "Add a database index",
                "Roll back the last deploy"
            ],
            "question": "What is the best fix?",
            "explanation": "The DB is healthy — the problem is every request rebuilding one key at once. Serialize the rebuild with a mutex/lock, or serve stale-while-revalidate so one worker refreshes while others use the old value. Jittering TTLs stops many keys expiring together.",
            "correct_index": 1
        }
    ],
    "evidence": {
        "logs": [
            {
                "t": "10:05:00",
                "msg": "cache key 'homepage:featured' expired (ttl reached)",
                "level": "INFO",
                "request_id": "cache"
            },
            {
                "t": "10:05:01",
                "msg": "1,842 concurrent requests recomputing homepage:featured — DB CPU 99%",
                "level": "WARN",
                "request_id": "cache"
            },
            {
                "t": "10:05:03",
                "msg": "cache key 'homepage:featured' repopulated",
                "level": "INFO",
                "request_id": "cache"
            }
        ],
        "metrics": [
            {
                "unit": "%",
                "title": "cache hit rate",
                "series": [
                    [
                        0,
                        98
                    ],
                    [
                        4,
                        97
                    ],
                    [
                        5,
                        18
                    ],
                    [
                        6,
                        92
                    ],
                    [
                        9,
                        97
                    ],
                    [
                        10,
                        96
                    ],
                    [
                        11,
                        15
                    ],
                    [
                        12,
                        94
                    ]
                ]
            },
            {
                "unit": "%",
                "title": "DB CPU",
                "series": [
                    [
                        0,
                        31
                    ],
                    [
                        4,
                        33
                    ],
                    [
                        5,
                        99
                    ],
                    [
                        6,
                        68
                    ],
                    [
                        9,
                        34
                    ],
                    [
                        10,
                        32
                    ],
                    [
                        11,
                        98
                    ],
                    [
                        12,
                        61
                    ]
                ],
                "threshold": 80
            }
        ],
        "scenario": "Every 5 minutes, like clockwork, database CPU jumps to ~100% and API latency spikes for a few seconds, then recovers. It lines up exactly with when a hot cache key expires. Look at the cache hit rate and DB CPU side by side."
    }
}
---

