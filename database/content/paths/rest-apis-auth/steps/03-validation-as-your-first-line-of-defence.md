---json
{
    "order": 3,
    "title": "Validation as your first line of defence",
    "type": "reading",
    "description": null,
    "tldr": "Validate at the edge with a FormRequest; Laravel auto-returns 422 with field errors. Unvalidated input is how a bad request becomes a 500.",
    "difficulty": "intermediate",
    "estimated_minutes": 10,
    "challenge_slug": null,
    "lab_url": null,
    "has_playground": false,
    "playground_starter_code": null,
    "challenge_prompt": null,
    "resources": null,
    "instructions": null,
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---
## Core — reject bad input before it reaches your logic

Never trust the client. In Laravel, validate at the edge:

```php
$data = $request->validate([
    'product_id' => ['required', 'integer', 'min:1'],
    'quantity'   => ['required', 'integer', 'between:1,100'],
    'currency'   => ['required', 'in:eur,brl'],
]);
```

If it fails, Laravel automatically responds **422 Unprocessable Entity** with a `{ message, errors }` body — no work from you. Only *valid* data continues past this line.

## Deeper dive — FormRequests keep controllers clean

Move rules into a dedicated `FormRequest` so the controller only handles the happy path:

```php
public function store(StoreOrderRequest $request) { /* $request->validated() is safe */ }
```

`FormRequest` also holds `authorize()` — validation and authorization in one place, off the controller.

## Senior insights — the 422-not-500 rule

The single most common junior bug: using unvalidated input (`$input['quantity'] * $price`) when `quantity` is missing → a `TypeError` → a **500**. The client can't tell what they did wrong. Validate first and that same failure becomes a **422 with a clear message**. You'll diagnose this exact bug from telemetry later in this track.
