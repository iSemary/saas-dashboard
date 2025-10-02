<?php

return [
    'name' => 'Subscription',
    
    // Default billing cycles available
    'billing_cycles' => [
        'monthly' => [
            'name' => 'Monthly',
            'days' => 30,
            'multiplier' => 1,
        ],
        'quarterly' => [
            'name' => 'Quarterly',
            'days' => 90,
            'multiplier' => 3,
        ],
        'semi_annually' => [
            'name' => 'Semi-Annually',
            'days' => 180,
            'multiplier' => 6,
        ],
        'annually' => [
            'name' => 'Annually',
            'days' => 365,
            'multiplier' => 12,
        ],
        'biennially' => [
            'name' => 'Biennially',
            'days' => 730,
            'multiplier' => 24,
        ],
        'triennially' => [
            'name' => 'Triennially',
            'days' => 1095,
            'multiplier' => 36,
        ],
        'lifetime' => [
            'name' => 'Lifetime',
            'days' => 36500,
            'multiplier' => 0,
        ],
    ],
    
    // Default trial settings
    'trial' => [
        'default_days' => 14,
        'requires_payment_method' => false,
        'auto_convert' => true,
        'grace_period_days' => 3,
    ],
    
    // Proration settings
    'proration' => [
        'enabled' => true,
        'method' => 'daily', // daily, monthly
        'credit_unused_time' => true,
    ],
    
    // Subscription settings
    'subscription' => [
        'auto_renew_default' => true,
        'dunning_attempts' => 3,
        'dunning_interval_days' => 3,
        'grace_period_days' => 7,
    ],
    
    // Feature limits
    'features' => [
        'max_features_per_plan' => 50,
        'max_plans_per_tenant' => 100,
    ],
    
    // Discount settings
    'discounts' => [
        'max_percentage' => 100,
        'stackable_limit' => 3,
        'code_length' => 8,
    ],
    
    // Currency settings
    'currency' => [
        'default' => env('DEFAULT_CURRENCY', 'USD'),
        'precision' => 2,
    ],
    
    // Notification settings
    'notifications' => [
        'trial_ending_days' => [7, 3, 1],
        'subscription_ending_days' => [30, 7, 1],
        'payment_failed_retry_days' => [1, 3, 7],
    ],
];
