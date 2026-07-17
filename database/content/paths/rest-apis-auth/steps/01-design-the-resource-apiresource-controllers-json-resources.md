---json
{
    "order": 1,
    "title": "Design the resource: apiResource, controllers & JSON Resources",
    "type": "reading",
    "description": null,
    "tldr": "Route::apiResource wires the 5 REST routes to a controller; a JsonResource controls exactly what shape leaves your API — never dump a raw model.",
    "difficulty": "intermediate",
    "estimated_minutes": 12,
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
## Core — from route to response in Laravel

One line gives you the whole REST surface:

```php
Route::apiResource('jobs', JobController::class);
```

That maps `index / store / show / update / destroy` to controller methods. Each method returns a **API Resource**, never the raw model:

```php
return JobResource::collection($jobs);   // list
return new JobResource($job);            // one
```

## Deeper dive — the Resource is your contract

A `JsonResource` decides exactly which fields — and which shape — leave your API:

```php
public function toArray($request): array
{
    return [
        'id'    => $this->id,
        'title' => $this->title,
        'company' => $this->when($this->relationLoaded('company'), fn () => $this->company->name),
    ];
}
```

Two habits that mark experience: use `$this->when(...)` so you never trigger an N+1 by touching an unloaded relation, and never expose internal columns (password hashes, soft-delete flags, foreign keys the client doesn't need).

## Senior insights — the envelope

Wrap lists in a consistent `{ data, meta }` shape so pagination is predictable — which is exactly the `paginate()` helper you'll build in the next step. A stable envelope is the difference between an API a team enjoys and one they curse.
