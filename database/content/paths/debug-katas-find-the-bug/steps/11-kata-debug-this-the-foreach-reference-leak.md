---json
{
    "order": 11,
    "title": "Kata: Debug This — The Foreach Reference Leak",
    "type": "challenge",
    "description": "One of PHP's most notorious gotchas: leaving a `foreach (...as &$value)` reference dangling after the loop ends, then reusing that same variable name in a later `foreach` — silently corrupting your own array. This is the kind of bug that survives code review because the second loop looks like harmless, do-nothing leftover code.",
    "tldr": null,
    "difficulty": null,
    "estimated_minutes": null,
    "challenge_slug": "debug-foreach-reference-leak",
    "lab_url": null,
    "has_playground": false,
    "playground_starter_code": null,
    "challenge_prompt": "A teammate says: \"The last two names in the list keep coming out identical, no matter what we submit.\" The tests are already written and both are failing. Find the bug in `normalizeNames()` — the fix is a single line, and it is not inside either loop's body.",
    "resources": [
        {
            "url": "https://www.php.net/manual/en/control-structures.foreach.php",
            "label": "PHP manual: references and foreach — the exact warning box on this bug"
        }
    ],
    "instructions": null,
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

