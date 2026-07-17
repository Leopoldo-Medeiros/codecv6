---json
{
    "order": 2,
    "title": "Lab: Building Your Branching Strategy",
    "type": "lab",
    "description": "Branching strategy is not a git question — it is a team coordination question. Trunk-based development reduces merge conflicts and enables continuous delivery; Gitflow supports long-running releases with separate hotfix lanes. Neither is universally correct. In this lab you will implement trunk-based development for a solo or small-team context and experience how short-lived branches eliminate the integration hell of long-lived feature branches.",
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
            "url": "https://trunkbaseddevelopment.com/",
            "label": "Trunk-Based Development"
        },
        {
            "url": "https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow",
            "label": "Gitflow Workflow"
        },
        {
            "url": "https://docs.github.com/en/get-started/using-github/github-flow",
            "label": "GitHub Flow"
        }
    ],
    "instructions": [
        {
            "id": 1,
            "text": "Set up a test repository with a `main` branch protected: require PR review, passing CI, no direct push"
        },
        {
            "id": 2,
            "text": "Create a feature branch named `feature/<issue-id>-short-description` from an up-to-date `main`"
        },
        {
            "id": 3,
            "text": "Implement a small change (a new function with a test), commit with a conventional commit message"
        },
        {
            "id": 4,
            "text": "While your branch is open, simulate a teammate merging to main — rebase your branch on the new main"
        },
        {
            "id": 5,
            "text": "Open a Pull Request: write a description with \"Why\", \"What changed\", and a test plan"
        },
        {
            "id": 6,
            "text": "Squash-merge the PR — verify that `main`'s history has one clean, descriptive commit"
        },
        {
            "id": 7,
            "text": "Delete the feature branch. Repeat with a hotfix branch directly from the latest release tag"
        }
    ],
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

