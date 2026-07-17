<?php

/**
 * "Coming soon" tracks we're measuring demand for before building. The slug is
 * the source of truth: the waitlist endpoint only accepts these keys, and the
 * admin dashboard labels signups with the title. Add a candidate = add an entry.
 *
 * See docs/architecture-review.md and the launch strategy: this is the
 * demand-validation gate — the market picks the wedge, we build the winner.
 */
return [
    'topics' => [
        'observability' => [
            'title' => 'Observability Track',
            'description' => 'Operate what you build — traces, metrics, logs, and incident debugging with OpenTelemetry.',
        ],
        'ai-for-devs' => [
            'title' => 'AI for Developers',
            'description' => 'Ship faster with AI coding assistants and agentic workflows, the way real teams use them.',
        ],
        'ai-for-support' => [
            'title' => 'AI for IT Support & TSE',
            'description' => 'Troubleshoot faster with AI — built for support engineers, TSEs and IT pros, not just developers.',
        ],
    ],
];
