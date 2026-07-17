---json
{
    "order": 6,
    "title": "Lab: secure an endpoint with auth + ownership",
    "type": "lab",
    "description": "Guided exercise: lock down an /orders API so only authenticated owners can read their own orders.",
    "tldr": null,
    "difficulty": "intermediate",
    "estimated_minutes": 25,
    "challenge_slug": null,
    "lab_url": null,
    "has_playground": false,
    "playground_starter_code": null,
    "challenge_prompt": null,
    "resources": null,
    "instructions": [
        {
            "id": 1,
            "text": "Wrap the /orders routes in the auth:sanctum middleware group. Confirm an unauthenticated request now returns 401 (try it with curl and no token)."
        },
        {
            "id": 2,
            "text": "In OrderController@show, load the order and compare its user_id to $request->user()->id. If they differ, throw an authorization error so the API returns 403 — not 404, not 200."
        },
        {
            "id": 3,
            "text": "Confirm the owner (valid token, own order) gets 200 with the JsonResource, and a different authenticated user gets 403."
        },
        {
            "id": 4,
            "text": "Bonus: scope the token with an ability (orders:read) and reject calls whose token lacks it with 403."
        }
    ],
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

