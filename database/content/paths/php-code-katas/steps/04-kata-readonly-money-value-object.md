---json
{
    "order": 4,
    "title": "Kata: Readonly Money Value Object",
    "type": "challenge",
    "description": "Immutability eliminates an entire class of bugs: two parts of the code holding a reference to the same object and mutating it from one side without the other knowing. PHP 8.1 `readonly` enforces this at the language level, making it impossible to mutate a property after construction. This kata builds the pattern for Value Objects — one of the most useful tools in domain-driven design.",
    "tldr": null,
    "difficulty": null,
    "estimated_minutes": null,
    "challenge_slug": "readonly-money-value-object",
    "lab_url": null,
    "has_playground": false,
    "playground_starter_code": null,
    "challenge_prompt": "A financial system stores prices as integers (cents) to avoid floating-point errors. The current code passes raw ints everywhere and the currency gets mixed up. Introduce a `Money` Value Object that makes currency mismatches a compile-time error and formatting a single method call.",
    "resources": [
        {
            "url": "https://php.watch/versions/8.1/readonly",
            "label": "PHP 8.1 readonly properties"
        },
        {
            "url": "https://martinfowler.com/bliki/ValueObject.html",
            "label": "Value Objects in DDD"
        },
        {
            "url": "https://www.martinfowler.com/eaaCatalog/money.html",
            "label": "Money pattern"
        }
    ],
    "instructions": null,
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

