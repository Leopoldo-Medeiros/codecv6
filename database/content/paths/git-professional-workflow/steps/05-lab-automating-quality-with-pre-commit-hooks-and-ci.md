---json
{
    "order": 5,
    "title": "Lab: Automating Quality with pre-commit Hooks and CI",
    "type": "lab",
    "description": "Humans forget to run the linter. Humans skip the tests when they are in a hurry. CI does not forget. By automating quality checks — formatting, static analysis, tests — at both the pre-commit hook and CI pipeline level, you remove the burden from humans and make quality the path of least resistance. We will set up a complete quality gate using GitHub Actions.",
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
            "url": "https://git-scm.com/book/en/v2/Customizing-Git-Git-Hooks",
            "label": "Git Hooks"
        },
        {
            "url": "https://docs.github.com/en/actions/quickstart",
            "label": "GitHub Actions — getting started"
        },
        {
            "url": "https://laravel.com/docs/pint",
            "label": "Laravel Pint — code formatter"
        }
    ],
    "instructions": [
        {
            "id": 1,
            "text": "Create a pre-commit hook (`.git/hooks/pre-commit`) that runs `./vendor/bin/pint --dirty` and fails if there are unfixed issues"
        },
        {
            "id": 2,
            "text": "Add a pre-push hook that runs `php artisan test --compact` — the push is blocked if tests fail"
        },
        {
            "id": 3,
            "text": "Create `.github/workflows/ci.yml` with three jobs: lint, test, and static-analysis (PHPStan or Larastan)"
        },
        {
            "id": 4,
            "text": "Configure branch protection: PRs can only be merged if all three CI jobs pass"
        },
        {
            "id": 5,
            "text": "Test the setup: intentionally introduce a formatting error, commit, and confirm the hook blocks you"
        },
        {
            "id": 6,
            "text": "Push the broken code — confirm the CI pipeline also catches it before it reaches `main`"
        }
    ],
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

