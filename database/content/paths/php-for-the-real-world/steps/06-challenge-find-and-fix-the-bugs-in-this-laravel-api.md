---json
{
    "order": 6,
    "title": "Challenge: Find and Fix the Bugs in This Laravel API",
    "type": "challenge",
    "description": "An e-commerce API is in production with critical bugs reported by users. You have access to the code and the logs. No stack trace provided — you need to find, reproduce and fix each problem. This is the real work of a backend developer.",
    "tldr": null,
    "difficulty": null,
    "estimated_minutes": null,
    "challenge_slug": "find-fix-laravel-api-bugs",
    "lab_url": null,
    "has_playground": false,
    "playground_starter_code": null,
    "challenge_prompt": "You are the only developer on call. It is 11pm and support has reported 3 critical production bugs affecting the checkout. The CEO is awake. You have 2 hours. Document every step — your investigation will be reviewed tomorrow.",
    "resources": [
        {
            "url": "https://laravel.com/docs/helpers#method-dd",
            "label": "Laravel Debugging with dd() and dump()"
        },
        {
            "url": "https://www.php.net/manual/en/errorfunc.constants.php",
            "label": "PHP Error Levels"
        }
    ],
    "instructions": [
        {
            "id": 1,
            "text": "Clone the challenge repository: github.com/codecv/challenge-laravel-bugs"
        },
        {
            "id": 2,
            "text": "Read the README with each user-reported bug description"
        },
        {
            "id": 3,
            "text": "Bug #1: \"Orders appear duplicated at checkout\" — find the root cause"
        },
        {
            "id": 4,
            "text": "Bug #2: \"Admin can delete their own account\" — fix the authorisation"
        },
        {
            "id": 5,
            "text": "Bug #3: \"API returns 500 when product is out of stock\" — add proper error handling"
        },
        {
            "id": 6,
            "text": "Write a regression test for each fixed bug"
        },
        {
            "id": 7,
            "text": "Open a PR with a description explaining: root cause, impact and solution"
        }
    ],
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

