---json
{
    "order": 6,
    "title": "Kata: Debug This — The Falsy Zero Trap",
    "type": "challenge",
    "description": "PHP's loose truthiness rules are a classic source of production bugs: `0`, `'0'`, `null`, `false`, and `[]` are all falsy, which means a perfectly valid return value can be silently treated as \"nothing happened.\" This kata is a real-world instance of that trap, hiding inside a permission check.",
    "tldr": null,
    "difficulty": null,
    "estimated_minutes": null,
    "challenge_slug": "debug-falsy-zero-trap",
    "lab_url": null,
    "has_playground": false,
    "playground_starter_code": null,
    "challenge_prompt": "A user whose FIRST permission in the list is the one being checked gets \"permission denied\" in production, even though they clearly have it. The tests are already written and one of them is failing. Find the bug in `hasPermission()` and fix it with a single operator change.",
    "resources": [
        {
            "url": "https://www.php.net/manual/en/function.array-search.php",
            "label": "array_search() — PHP manual (read the return value section carefully)"
        },
        {
            "url": "https://www.php.net/manual/en/types.comparisons.php",
            "label": "PHP type comparison tables"
        }
    ],
    "instructions": null,
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

