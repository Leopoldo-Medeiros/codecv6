---json
{
    "order": 5,
    "title": "Kata: Memory-Efficient Generator Pagination",
    "type": "challenge",
    "description": "A background job that exports 2 million rows by loading them all into memory will crash in production. PHP generators allow you to process arbitrarily large datasets with constant memory: yield one row at a time, only fetching the next page when the consumer asks for it. This kata trains the mental model of lazy evaluation.",
    "tldr": null,
    "difficulty": null,
    "estimated_minutes": null,
    "challenge_slug": "generator-paginated-rows",
    "lab_url": null,
    "has_playground": false,
    "playground_starter_code": null,
    "challenge_prompt": "A nightly report job is failing with \"Allowed memory size exhausted\" on a customer with 800k records. Loading everything into an array is not an option. Rewrite the data-fetching layer using a generator so it never holds more than one page in memory.",
    "resources": [
        {
            "url": "https://www.php.net/manual/en/language.generators.overview.php",
            "label": "PHP Generators — official docs"
        },
        {
            "url": "https://www.php.net/manual/en/language.generators.syntax.php",
            "label": "Generators in depth"
        },
        {
            "url": "https://tideways.com/profiler/blog/how-much-memory-does-your-php-application-use",
            "label": "Memory usage in PHP — practical guide"
        }
    ],
    "instructions": null,
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

