---json
{
    "order": 0,
    "title": "What \"RESTful\" really means (and what interviewers check)",
    "type": "reading",
    "description": null,
    "tldr": "REST is resources (nouns) + HTTP verbs + honest status codes, with no server-side session between calls. Interviewers probe exactly those four things.",
    "difficulty": "beginner",
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
    "quiz": null,
    "evidence": null
}
---
## Core — the four ideas

An API is **RESTful** when it models your domain as **resources** (nouns like `orders`, `jobs`, `users`) that you act on with **HTTP verbs**:

- `GET /jobs` — list · `GET /jobs/42` — read one
- `POST /jobs` — create · `PUT/PATCH /jobs/42` — update · `DELETE /jobs/42` — remove

Two more rules matter as much as the URLs:

- **Honest status codes.** `200/201` success, `422` validation failed, `401` not authenticated, `403` authenticated-but-forbidden, `404` not found, `500` you broke.
- **Stateless.** Each request carries everything it needs (usually a token). The server keeps no per-client session between calls.

## Deeper dive — the parts that separate a real API from "some JSON over HTTP"

- **Consistency.** Every list looks the same (`{ data, meta }`), every error looks the same (`{ message, errors }`). Consistency is what lets a front-end team move fast against you.
- **Idempotency.** `GET`, `PUT`, and `DELETE` should be safe to retry; `POST` isn't. This matters the moment a network blips and a client retries.
- **Versioning.** `/api/v1/...` — so you can evolve without breaking existing clients.

## Senior insights — what an interviewer is really testing

When someone asks "is your API RESTful?", they're checking whether you'll **return the right status code under failure** and **keep contracts stable**. The junior answer describes verbs; the senior answer talks about error envelopes, idempotent retries, and not leaking a 500 when the real problem was bad input (you'll fix exactly that in this track's incident).
