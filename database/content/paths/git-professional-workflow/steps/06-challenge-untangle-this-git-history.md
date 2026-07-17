---json
{
    "order": 6,
    "title": "Challenge: Untangle This Git History",
    "type": "challenge",
    "description": "A repository has been used without any conventions for 6 months. The history is a mix of merge commits, \"fix\", \"wip\", \"test2\" commits, and a feature that was half-implemented and never finished. Your task: clean up a branch so it is ready for a production merge, using interactive rebase, fixup commits, and a proper PR description.",
    "tldr": null,
    "difficulty": null,
    "estimated_minutes": null,
    "challenge_slug": "untangle-git-history",
    "lab_url": null,
    "has_playground": false,
    "playground_starter_code": null,
    "challenge_prompt": "You have inherited a repository from a developer who just left the company. There is a payment feature branch that \"is almost done\" according to their last message 3 weeks ago. Before you can merge it, you need to understand it — and the history looks like a war zone. Clean it up.",
    "resources": [
        {
            "url": "https://git-scm.com/docs/git-rebase",
            "label": "git rebase --interactive"
        },
        {
            "url": "https://git-scm.com/docs/git-commit#Documentation/git-commit.txt---fixupamaboreredit-1ltcommitgt",
            "label": "git commit --fixup"
        },
        {
            "url": "https://git-scm.com/book/en/v2/Git-Tools-Rewriting-History",
            "label": "Rewriting History — Pro Git"
        }
    ],
    "instructions": [
        {
            "id": 1,
            "text": "Clone the challenge repository and inspect the log: `git log --oneline --graph main..feature/payment`"
        },
        {
            "id": 2,
            "text": "Identify which commits belong together semantically (e.g., \"fix\" commits that are really part of a prior commit)"
        },
        {
            "id": 3,
            "text": "Use `git rebase -i main` to squash, fixup, reorder, and rename commits — aim for 3-5 clean commits"
        },
        {
            "id": 4,
            "text": "Each final commit must: pass the tests, have a Conventional Commit message, and contain only logically related changes"
        },
        {
            "id": 5,
            "text": "Write a PR description for the cleaned branch: what it does, why, and how to test it"
        },
        {
            "id": 6,
            "text": "Push the clean branch and verify the CI pipeline passes end-to-end"
        }
    ],
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

