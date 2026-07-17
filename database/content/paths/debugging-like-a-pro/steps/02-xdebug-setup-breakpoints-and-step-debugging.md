---json
{
    "order": 2,
    "title": "Xdebug: Setup, Breakpoints and Step Debugging",
    "type": "lab",
    "description": "`var_dump` is the tool of someone who does not know Xdebug. With Xdebug and VS Code (or PhpStorm), you can pause execution at any line, inspect the full application state, and step through code line by line while watching variables change. This transforms debugging from guessing into investigation.",
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
            "url": "https://xdebug.org/docs/step_debug",
            "label": "Xdebug 3 — official docs"
        },
        {
            "url": "https://marketplace.visualstudio.com/items?itemName=xdebug.php-debug",
            "label": "VS Code PHP Debug extension"
        },
        {
            "url": "https://xdebug.org/docs/install",
            "label": "Setting up Xdebug in Docker"
        }
    ],
    "instructions": [
        {
            "id": 1,
            "text": "Verify Xdebug is installed: `php -v` should show \"with Xdebug v3\""
        },
        {
            "id": 2,
            "text": "Configure VS Code: install the \"PHP Debug\" extension and create launch.json with port 9003"
        },
        {
            "id": 3,
            "text": "Add a breakpoint on line 1 of a Laravel route and trigger it via curl"
        },
        {
            "id": 4,
            "text": "Practice Step Over (F10), Step Into (F11), and Step Out (Shift+F11)"
        },
        {
            "id": 5,
            "text": "Add a Watch Expression to monitor a specific variable"
        },
        {
            "id": 6,
            "text": "Use Conditional Breakpoints: pause only when `$user->id === 5`"
        },
        {
            "id": 7,
            "text": "Demonstrate full N+1 query debugging using Eloquent breakpoints"
        }
    ],
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

