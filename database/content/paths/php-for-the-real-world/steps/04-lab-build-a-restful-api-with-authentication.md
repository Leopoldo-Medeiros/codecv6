---json
{
    "order": 4,
    "title": "Lab: Build a RESTful API with Authentication",
    "type": "lab",
    "description": "Put everything into practice by building a task management API: Sanctum authentication, full CRUD, authorization policies, Form Requests with validation, API Resources for response transformation, and pagination. The focus is not on speed — it is on making every decision intentionally.",
    "tldr": null,
    "difficulty": null,
    "estimated_minutes": null,
    "challenge_slug": null,
    "lab_url": null,
    "has_playground": false,
    "playground_starter_code": null,
    "challenge_prompt": null,
    "resources": [
        {
            "url": "https://laravel.com/docs/sanctum",
            "label": "Laravel Sanctum"
        },
        {
            "url": "https://laravel.com/docs/eloquent-resources",
            "label": "API Resources"
        },
        {
            "url": "https://laravel.com/docs/validation#form-request-validation",
            "label": "Form Request Validation"
        }
    ],
    "instructions": [
        {
            "id": 1,
            "text": "Create a new Laravel project: `composer create-project laravel/laravel task-api`"
        },
        {
            "id": 2,
            "text": "Configure Sanctum and create the users migration with roles (admin, user)"
        },
        {
            "id": 3,
            "text": "Create the Task model with: title, description, status (pending/in_progress/done), due_date, user_id"
        },
        {
            "id": 4,
            "text": "Implement TaskController with index, store, show, update, destroy — using Form Request for validation"
        },
        {
            "id": 5,
            "text": "Add a Policy: users can only view and edit their own tasks"
        },
        {
            "id": 6,
            "text": "Create TaskResource with date transformation (Carbon) and computed field `is_overdue`"
        },
        {
            "id": 7,
            "text": "Write 3 Feature Tests covering: creating a task, listing only own tasks, and an unauthorised attempt"
        }
    ],
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

