---json
{
    "order": 7,
    "title": "Checkpoint: Modern PHP Reflexes",
    "type": "quiz",
    "description": "A quick knowledge check before you move on. These questions cover the language features the katas above drilled — null safety, match expressions, enums, and value objects. Answer all of them correctly to clear the checkpoint.",
    "tldr": null,
    "difficulty": null,
    "estimated_minutes": null,
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
                "?? (null coalescing)",
                "?-> (nullsafe)",
                "-> (object access)",
                "?: (elvis)"
            ],
            "question": "Which operator short-circuits a chain the moment a link is null, returning null instead of throwing?",
            "explanation": "The nullsafe operator ?-> stops evaluating the chain and yields null as soon as any accessed member is null. ?? only supplies a fallback for an already-evaluated null.",
            "correct_index": 1
        },
        {
            "id": 2,
            "options": [
                "It can loop over cases",
                "Strict (===) comparison and it returns a value",
                "It runs faster at compile time",
                "It allows fall-through between cases"
            ],
            "question": "What does match() give you that a classic switch does not?",
            "explanation": "match uses strict === comparison (no type juggling), returns a value directly, and throws UnhandledMatchError when nothing matches — unlike switch, which uses loose comparison and falls through.",
            "correct_index": 1
        },
        {
            "id": 3,
            "options": [
                "Enums use less memory",
                "Illegal states and transitions become unrepresentable",
                "Strings cannot be stored in a database",
                "Enums are automatically cached"
            ],
            "question": "Why model an invoice lifecycle with a backed enum plus transition methods rather than plain string constants?",
            "explanation": "Encoding valid transitions inside the enum makes invalid transitions impossible to express — the type system enforces the state machine, so a Draft invoice can never jump straight to Paid.",
            "correct_index": 1
        },
        {
            "id": 4,
            "options": [
                "It is stored by reference",
                "Its properties cannot be mutated after construction",
                "It is always a singleton",
                "It bypasses the garbage collector"
            ],
            "question": "What makes a readonly value object like Money safe to pass around?",
            "explanation": "readonly properties can only be set once, during construction. The object is immutable, so no caller can secretly change its amount or currency after you hand it out.",
            "correct_index": 1
        }
    ],
    "evidence": null
}
---

