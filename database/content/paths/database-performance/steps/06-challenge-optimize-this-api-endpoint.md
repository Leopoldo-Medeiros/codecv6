---json
{
    "order": 6,
    "title": "Challenge: Optimize This API Endpoint",
    "type": "challenge",
    "description": "A product listings endpoint returns in 6.4 seconds at p95. You have the source code, EXPLAIN output, and a benchmark script. The target SLA is 200ms. No infrastructure changes — the solution must be in the code and schema.",
    "tldr": null,
    "difficulty": null,
    "estimated_minutes": null,
    "challenge_slug": "optimize-api-endpoint",
    "lab_url": null,
    "has_playground": false,
    "playground_starter_code": null,
    "challenge_prompt": "The VP of Product sent a screenshot from PageSpeed Insights: product listing page, 6.4 second TTFB, Performance score 23. The e-commerce launch is in 3 days. You have been asked to fix it without provisioning new infrastructure. What is your plan?",
    "resources": [
        {
            "url": "https://laravel.com/docs/artisan#tinker",
            "label": "Laravel Artisan tinker for query testing"
        }
    ],
    "instructions": [
        {
            "id": 1,
            "text": "Clone the challenge repository and seed 100k products with realistic data distributions"
        },
        {
            "id": 2,
            "text": "Run the benchmark: `ab -n 100 -c 10 http://localhost/api/products` — record the p95 baseline"
        },
        {
            "id": 3,
            "text": "Enable `DB::enableQueryLog()` and identify every query the endpoint executes"
        },
        {
            "id": 4,
            "text": "Run EXPLAIN on each query — document the scan type, rows examined, and whether an index is used"
        },
        {
            "id": 5,
            "text": "Fix all issues found (indexes, eager loading, unnecessary columns, missing pagination)"
        },
        {
            "id": 6,
            "text": "Re-run the benchmark and confirm p95 < 200ms"
        },
        {
            "id": 7,
            "text": "Write a 1-page technical report: what you found, why it was slow, what you changed, and the measured improvement"
        }
    ],
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

