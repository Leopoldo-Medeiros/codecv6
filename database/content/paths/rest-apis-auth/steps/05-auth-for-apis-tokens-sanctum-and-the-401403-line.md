---json
{
    "order": 5,
    "title": "Auth for APIs: tokens, Sanctum and the 401/403 line",
    "type": "reading",
    "description": null,
    "tldr": "401 = who are you? (no/invalid token). 403 = I know you, but no. Sanctum issues bearer tokens; middleware guards routes; abilities scope what a token can do.",
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
    "quiz": null,
    "evidence": null
}
---
## Core — authentication vs authorization

Two different questions, two different status codes:

- **Authentication** — *who are you?* No or invalid token → **401 Unauthorized**.
- **Authorization** — *are you allowed to do this?* Valid token, wrong permission → **403 Forbidden**.

Mixing them up is a classic junior tell. "Logged in but not allowed" is **403**, never 401.

## Deeper dive — Sanctum in practice

Laravel Sanctum issues a **bearer token** on login; the client sends it as `Authorization: Bearer <token>`. Guard routes with middleware:

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('orders', OrderController::class);
});
```

Scope what a token can do with **abilities**: `$user->createToken('mobile', ['orders:read'])`, then check `$request->user()->tokenCan('orders:read')`.

## Senior insights — the threat model

Think about token **expiry and revocation** (a leaked token that never dies is a breach), and about **ownership**: authenticated isn't enough — user A must not read user B's orders. That ownership check (return 403 otherwise) is the lab you'll do next, and it's the pattern this very platform uses to stop one client seeing another's data.
