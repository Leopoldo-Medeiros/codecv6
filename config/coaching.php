<?php

/*
| Coaching upsell nudges (practice funnel F6 — "Get Coached").
|
| CoachingRecommendationService reads a user's practice signals (XP, streak,
| challenges completed, paths completed) and recommends the single most
| relevant coaching tier they haven't bought yet. This file holds the tunable
| numbers and copy; the ladder *logic* lives in the service.
|
| `priority` is evaluated top-to-bottom — the first tier the user qualifies
| for (any one of its thresholds met) and hasn't already purchased wins.
| Thresholds are matched with OR semantics: hitting any single one triggers.
| Copy is English-only per the project language policy.
*/

return [
    // Highest-value / highest-readiness first. First qualifying tier wins.
    'priority' => ['mentorship', 'accelerator', 'bootcamp'],

    'nudges' => [
        // Power users who've shown real discipline — the 1-on-1 play.
        'mentorship' => [
            'headline' => 'You practice like a pro',
            'body' => "You've built a serious practice habit. A 1-on-1 mentor can fast-track you from solid to senior with a plan built around your goals.",
            'cta' => 'Explore mentorship',
            'thresholds' => [
                'longest_streak' => 7,
                'xp' => 300,
            ],
        ],

        // Has demonstrable skills — convert them into interviews (entry tier).
        'accelerator' => [
            'headline' => 'Turn your skills into interviews',
            'body' => "You've proven real skills here. Let's get your CV and LinkedIn recruiter-ready so they open the right doors.",
            'cta' => 'Get CV-ready',
            'thresholds' => [
                'paths_completed' => 1,
                'challenges' => 5,
            ],
        ],

        // Committed beginner building momentum — offer structure and depth.
        'bootcamp' => [
            'headline' => 'Ready to go deeper?',
            'body' => "You're building real momentum. The Laravel + New Relic cohort gives you structure, production-grade projects, and a peer group to grow with.",
            'cta' => 'See the cohort',
            'thresholds' => [
                'challenges' => 3,
            ],
        ],
    ],
];
