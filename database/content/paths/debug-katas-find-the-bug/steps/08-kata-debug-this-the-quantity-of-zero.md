---json
{
    "order": 8,
    "title": "Kata: Debug This — The Quantity of Zero",
    "type": "challenge",
    "description": "A quantity field should be rejected only when it is completely missing from the request — a submitted value of zero is valid and present. The validator currently treats \"present but zero\" the same as \"never submitted.\"",
    "tldr": null,
    "difficulty": null,
    "estimated_minutes": null,
    "challenge_slug": "debug-validate-quantity",
    "lab_url": null,
    "has_playground": false,
    "playground_starter_code": null,
    "challenge_prompt": "A customer submitting `quantity: 0` gets a \"Quantity is required\" error, even though they clearly submitted a value. One of the tests already encodes the correct behaviour and is failing. Find the bug in `validateQuantity()` and fix it.",
    "resources": [
        {
            "url": "https://www.php.net/manual/en/function.empty.php",
            "label": "empty() vs isset() vs array_key_exists() — PHP manual comparison"
        }
    ],
    "instructions": null,
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

