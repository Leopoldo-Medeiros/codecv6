---json
{
    "order": 9,
    "title": "Kata: Debug This — The Discount That Disappeared",
    "type": "challenge",
    "description": "A function is supposed to apply per-product discount overrides on top of a base list, keyed by product ID, only replacing the products named in the override list. In production, the override goes missing entirely and every product ID lookup on the result breaks.",
    "tldr": null,
    "difficulty": null,
    "estimated_minutes": null,
    "challenge_slug": "debug-discount-overrides",
    "lab_url": null,
    "has_playground": false,
    "playground_starter_code": null,
    "challenge_prompt": "After applying an override for product 101, `$result[101]` does not exist anymore. The test already encodes the expected keys and values and is failing. Find the bug in `applyDiscountOverrides()` and fix it — the fix is a different built-in function, not new logic.",
    "resources": [
        {
            "url": "https://www.php.net/manual/en/function.array-replace.php",
            "label": "array_merge() vs array_replace() — PHP manual (read the integer-key behaviour carefully)"
        }
    ],
    "instructions": null,
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

