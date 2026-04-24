<?php

return [
    'name' => 'Payment',
    
    // Default environment for payment processing
    'environment' => env('PAYMENT_ENVIRONMENT', 'production'), // sandbox or production
    
    // Default currency
    'default_currency' => env('PAYMENT_DEFAULT_CURRENCY', 'USD'),
    
    // Merchant information
    'merchant_country' => env('PAYMENT_MERCHANT_COUNTRY', 'US'),
    'merchant_name' => env('PAYMENT_MERCHANT_NAME', config('app.name')),
    
    // Transaction limits
    'limits' => [
        'min_transaction_amount' => env('PAYMENT_MIN_AMOUNT', 0.50),
        'max_transaction_amount' => env('PAYMENT_MAX_AMOUNT', 100000),
        'daily_per_customer' => env('PAYMENT_DAILY_LIMIT', 50000),
        'monthly_per_customer' => env('PAYMENT_MONTHLY_LIMIT', 200000),
    ],
    
    // Business hours (optional)
    'business_hours' => [
        'enabled' => env('PAYMENT_BUSINESS_HOURS_ENABLED', false),
        'start' => env('PAYMENT_BUSINESS_HOURS_START', 9), // 9 AM
        'end' => env('PAYMENT_BUSINESS_HOURS_END', 17), // 5 PM
        'days' => [1, 2, 3, 4, 5], // Monday to Friday
        'timezone' => env('PAYMENT_BUSINESS_HOURS_TIMEZONE', 'UTC'),
    ],
    
    // Refund settings
    'refund_time_limit_days' => env('PAYMENT_REFUND_TIME_LIMIT', 180), // 6 months
    
    // Currency conversion
    'currency_conversion_fee' => env('PAYMENT_CURRENCY_CONVERSION_FEE', 0.5), // 0.5%
    
    // Risk management
    'high_risk_threshold' => env('PAYMENT_HIGH_RISK_THRESHOLD', 10000),
    'high_risk_countries' => explode(',', env('PAYMENT_HIGH_RISK_COUNTRIES', '')),
    'risk_fee_rate' => env('PAYMENT_RISK_FEE_RATE', 0.5), // 0.5%
    
    // Webhook settings
    'webhook' => [
        'timeout' => env('PAYMENT_WEBHOOK_TIMEOUT', 30),
        'retry_attempts' => env('PAYMENT_WEBHOOK_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('PAYMENT_WEBHOOK_RETRY_DELAY', 60), // seconds
    ],
    
    // Logging
    'logging' => [
        'enabled' => env('PAYMENT_LOGGING_ENABLED', true),
        'level' => env('PAYMENT_LOGGING_LEVEL', 'info'),
        'retention_days' => env('PAYMENT_LOG_RETENTION_DAYS', 90),
    ],
    
    // Cache settings
    'cache' => [
        'exchange_rates_ttl' => env('PAYMENT_CACHE_EXCHANGE_RATES_TTL', 3600), // 1 hour
        'gateway_info_ttl' => env('PAYMENT_CACHE_GATEWAY_INFO_TTL', 86400), // 24 hours
        'fee_estimates_ttl' => env('PAYMENT_CACHE_FEE_ESTIMATES_TTL', 300), // 5 minutes
    ],
    
    // External services
    'services' => [
        'fixer' => [
            'key' => env('FIXER_API_KEY'),
        ],
        'currencylayer' => [
            'key' => env('CURRENCYLAYER_API_KEY'),
        ],
    ],
    
    // Feature flags
    'features' => [
        'routing_optimization' => env('PAYMENT_FEATURE_ROUTING_OPTIMIZATION', true),
        'fraud_detection' => env('PAYMENT_FEATURE_FRAUD_DETECTION', true),
        'analytics' => env('PAYMENT_FEATURE_ANALYTICS', true),
        'webhooks' => env('PAYMENT_FEATURE_WEBHOOKS', true),
    ],
];