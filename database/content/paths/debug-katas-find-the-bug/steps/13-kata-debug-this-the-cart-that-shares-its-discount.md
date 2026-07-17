---json
{
    "order": 13,
    "title": "Kata: Debug This — The Cart That Shares Its Discount",
    "type": "challenge",
    "description": "Cloning a shopping cart is supposed to produce two fully independent carts. QA found that changing the clone's discount also silently changes the original cart's discount — as if they were the same object. PHP's `clone` keyword only performs a shallow copy: nested objects stay shared between the original and the clone unless `__clone()` says otherwise.",
    "tldr": null,
    "difficulty": null,
    "estimated_minutes": null,
    "challenge_slug": "debug-shopping-cart-clone",
    "lab_url": null,
    "has_playground": false,
    "playground_starter_code": null,
    "challenge_prompt": "Giving a cloned cart a new discount also changes the original cart's discount. One of the tests already encodes the expected independence and is failing. Find the bug in `ShoppingCart::__clone()` and fix it.",
    "resources": [
        {
            "url": "https://www.php.net/manual/en/language.oop5.cloning.php",
            "label": "Object Cloning — PHP manual"
        }
    ],
    "instructions": null,
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

