---json
{
    "order": 1,
    "title": "Why Juniors Struggle to Debug (and How to Fix That)",
    "type": "reading",
    "description": "The problem is not lack of effort — it is lack of method. Most junior devs add `var_dump` randomly until the bug disappears. This module introduces the scientific method of debugging: reproduce reliably, isolate variables, form a hypothesis, test, confirm. You will never debug in the dark again.\n\n## Two Approaches to the Same Bug\n\n```mermaid\nflowchart TB\n    subgraph junior[\"❌  Junior: Random Search\"]\n        J1([\"🐛 Bug found\"]):::red\n        J2[\"Add var_dump\\nsomewhere\"]:::red\n        J3[\"Run and check\\noutput\"]:::red\n        J4{\"Bug gone?\"}:::slate\n        J5([\"🤞 Commit and hope\"]):::red\n        J1 --> J2 --> J3 --> J4\n        J4 -->|No| J2\n        J4 -->|Yes| J5\n    end\n\n    subgraph senior[\"✅  Scientific Method\"]\n        S1([\"🐛 Bug found\"]):::emerald\n        S2[\"Reproduce\\nreliably\"]:::emerald\n        S3[\"Isolate variables\\n(binary search)\"]:::emerald\n        S4[\"Form a\\nhypothesis\"]:::emerald\n        S5[\"Test with Xdebug\\nor structured log\"]:::emerald\n        S6{\"Hypothesis\\ncorrect?\"}:::slate\n        S7[\"Fix + verify\\nno regression\"]:::emerald\n        S8([\"✅ Commit with confidence\"]):::emerald\n        S1 --> S2 --> S3 --> S4 --> S5 --> S6\n        S6 -->|No| S4\n        S6 -->|Yes| S7 --> S8\n    end\n\n    classDef emerald fill:#d1fae5,stroke:#059669,color:#065f46,font-weight:600\n    classDef slate   fill:#f1f5f9,stroke:#64748b,color:#1e293b,font-weight:500\n    classDef red     fill:#fee2e2,stroke:#ef4444,color:#991b1b,font-weight:500\n```\n\nThe junior loop is not wrong — it is just **unguided**. The scientific method forces you to think before acting, which is what separates a 30-minute fix from a 3-hour one.",
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
            "url": "https://www.debuggingbook.org/",
            "label": "The Art of Debugging — pragmatic approach"
        },
        {
            "url": "https://rubberduckdebugging.com/",
            "label": "Rubber Duck Debugging"
        },
        {
            "url": "https://jvns.ca/blog/2022/12/21/new-zine--how-debugging-works/",
            "label": "How to Debug — Julia Evans"
        }
    ],
    "instructions": null,
    "prerequisites": null,
    "concepts": null,
    "quiz": null,
    "evidence": null
}
---

