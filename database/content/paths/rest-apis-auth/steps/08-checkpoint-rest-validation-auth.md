---json
{
    "order": 8,
    "title": "Checkpoint: REST, validation & auth",
    "type": "quiz",
    "description": "Lock in the status-code reflexes an interviewer will probe.",
    "tldr": null,
    "difficulty": "intermediate",
    "estimated_minutes": 6,
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
                "200",
                "301",
                "404",
                "500"
            ],
            "question": "A client requests a resource that does not exist. Correct status code?",
            "explanation": "404 Not Found — the request was fine, the thing just is not there.",
            "correct_index": 2
        },
        {
            "id": 2,
            "options": [
                "200",
                "400",
                "422",
                "500"
            ],
            "question": "A POST fails validation (a required field is missing). Correct status code?",
            "explanation": "422 Unprocessable Entity — Laravel's default for failed validation, with a { message, errors } body.",
            "correct_index": 2
        },
        {
            "id": 3,
            "options": [
                "401",
                "403",
                "404",
                "200"
            ],
            "question": "A valid, authenticated user tries an action they are not permitted to do. Status?",
            "explanation": "403 Forbidden — we know who you are; you just are not allowed.",
            "correct_index": 1
        },
        {
            "id": 4,
            "options": [
                "400",
                "401",
                "403",
                "404"
            ],
            "question": "A request arrives with no token (or an invalid one). Status?",
            "explanation": "401 Unauthorized — authentication failed; who are you?",
            "correct_index": 1
        }
    ],
    "evidence": null
}
---

