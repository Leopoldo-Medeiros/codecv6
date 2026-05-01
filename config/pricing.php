<?php

/*
| Pricing tiers for Stripe Checkout.
|
| Amounts are in MINOR units (cents for EUR, centavos for BRL).
| Currencies follow ISO 4217 lowercase (Stripe convention).
| `recurring` marks the tier as a subscription (driven by interval).
*/

return [
    'tiers' => [
        'accelerator' => [
            'name' => 'Career Accelerator',
            'description' => 'CV writing, LinkedIn optimisation, and cover letter for IT professionals.',
            'recurring' => false,
            'prices' => [
                'eur' => 9900,
                'brl' => 39700,
            ],
        ],
        'bootcamp' => [
            'name' => 'Laravel + NR Bootcamp',
            'description' => 'Cohort-based 12-16 week training on Laravel architecture and New Relic observability.',
            'recurring' => false,
            'prices' => [
                'eur' => 150000,
                'brl' => 599000,
            ],
        ],
        'mentorship' => [
            'name' => '1-on-1 Mentorship',
            'description' => 'Bi-weekly 60-minute video coaching sessions plus WhatsApp support.',
            'recurring' => true,
            'interval' => 'month',
            'prices' => [
                'eur' => 24900,
                'brl' => 99700,
            ],
        ],
    ],

    'default_currency' => 'eur',
    'supported_currencies' => ['eur', 'brl'],
];
