---json
{
    "order": 6,
    "title": "Kata: Enum-Driven State Machine",
    "type": "challenge",
    "description": "State machines are everywhere: invoice workflows, order lifecycles, subscription statuses. Encoding valid transitions inside the enum that represents those states is elegant and safe — the compiler enforces that you handle every case, and invalid transitions return `null` instead of corrupting data. This is a premium kata that simulates a real domain modeling challenge.",
    "tldr": null,
    "difficulty": null,
    "estimated_minutes": null,
    "challenge_slug": "enum-driven-state-machine",
    "lab_url": null,
    "has_playground": false,
    "playground_starter_code": null,
    "challenge_prompt": "The billing team discovered that invoices can be moved from any state to any other state — there is no validation. Draft invoices are being marked as Paid directly, skipping the approval step. Model the invoice lifecycle as a proper state machine where illegal transitions are simply not possible.",
    "resources": [
        {
            "url": "https://www.php.net/manual/en/language.enumerations.php",
            "label": "PHP 8.1 Enums"
        },
        {
            "url": "https://refactoring.guru/design-patterns/state",
            "label": "State Machine pattern"
        },
        {
            "url": "https://pragprog.com/titles/swdddf/domain-modeling-made-functional/",
            "label": "Domain Modelling Made Functional"
        }
    ],
    "instructions": null,
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

