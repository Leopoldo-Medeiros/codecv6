---json
{
    "order": 3,
    "title": "Reading Stack Traces Like a Detective",
    "type": "reading",
    "description": "A stack trace is a crime scene map — each line tells you where the system was and what it was doing when the error occurred. Most juniors read only the first line. In this module you will learn to read from bottom to top, identify your code frames vs vendor frames, understand chained exceptions, and extract the context that matters.\n\n## How an Exception Travels Through Your Call Stack\n\n```mermaid\nsequenceDiagram\n    autonumber\n    participant C as Controller\n    participant S as UserService\n    participant R as UserRepository\n    participant DB as Database\n\n    Note over C, DB: A request arrives — the bug lives deep in the stack\n\n    C->>+S: getUser(id: 404)\n    S->>+R: find(id: 404)\n    R->>+DB: SELECT * FROM users WHERE id = 404\n    DB-->>-R: empty result set\n\n    rect rgb(254, 226, 226)\n        Note right of R: Frame 1 ← root cause\n        R-->>-S: throw ModelNotFoundException\n    end\n\n    rect rgb(241, 245, 249)\n        Note right of S: Frame 2\n        S-->>-C: exception propagates up\n    end\n\n    rect rgb(209, 250, 229)\n        Note right of C: Frame 3 ← top of stack trace\n        C-->>C: rendered as 500 response\n    end\n\n    Note over C, DB: Stack traces read bottom→top — Frame 1 is always the origin\n```\n\nYour code frames are the ones under `app/`. Vendor frames (under `vendor/`) show you the path, but the bug is almost always in **your** code or in how you called the library.",
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
            "url": "https://rollbar.com/blog/php-stack-trace/",
            "label": "How to Read a Stack Trace"
        },
        {
            "url": "https://www.php.net/manual/en/language.exceptions.php",
            "label": "PHP Exceptions — official docs"
        },
        {
            "url": "https://flareapp.io/docs/ignition/introduction",
            "label": "Ignition — Laravel error page explained"
        }
    ],
    "instructions": null,
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

