<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Monitoring Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for application monitoring,
    | health checks, and performance tracking.
    |
    */

    'enabled' => env('MONITORING_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Health Check Endpoints
    |--------------------------------------------------------------------------
    */
    'health_checks' => [
        'database' => [
            'enabled' => true,
            'timeout' => 5,
            'query' => 'SELECT 1',
        ],
        'cache' => [
            'enabled' => true,
            'timeout' => 3,
            'key' => 'health_check',
        ],
        'storage' => [
            'enabled' => true,
            'timeout' => 5,
            'test_file' => 'health_check.txt',
        ],
        'queue' => [
            'enabled' => env('QUEUE_CONNECTION') !== 'sync',
            'timeout' => 10,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Monitoring
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'enabled' => true,
        'sample_rate' => 0.1, // 10% of requests
        'thresholds' => [
            'response_time' => 1000, // milliseconds
            'memory_usage' => 128, // MB
            'database_queries' => 100,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Tracking
    |--------------------------------------------------------------------------
    */
    'error_tracking' => [
        'enabled' => true,
        'log_level' => 'error',
        'notify_on' => ['error', 'critical'],
        'exclude_paths' => [
            '/health',
            '/monitoring',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Uptime Monitoring
    |--------------------------------------------------------------------------
    */
    'uptime' => [
        'enabled' => true,
        'check_interval' => 60, // seconds
        'endpoints' => [
            '/',
            '/en',
            '/ar',
            '/health',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Monitoring
    |--------------------------------------------------------------------------
    */
    'database' => [
        'enabled' => true,
        'slow_query_threshold' => 1000, // milliseconds
        'connection_timeout' => 5,
        'max_connections' => 100,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Monitoring
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => true,
        'hit_rate_threshold' => 0.8, // 80%
        'memory_usage_threshold' => 0.9, // 90%
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Monitoring
    |--------------------------------------------------------------------------
    */
    'queue' => [
        'enabled' => env('QUEUE_CONNECTION') !== 'sync',
        'failed_jobs_threshold' => 10,
        'queue_size_threshold' => 1000,
    ],

    /*
    |--------------------------------------------------------------------------
    | External Services
    |--------------------------------------------------------------------------
    */
    'external_services' => [
        'mail' => [
            'enabled' => true,
            'timeout' => 10,
        ],
        'storage' => [
            'enabled' => true,
            'timeout' => 15,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'channels' => [
            'mail' => env('MONITORING_MAIL_ENABLED', false),
            'slack' => env('MONITORING_SLACK_ENABLED', false),
            'webhook' => env('MONITORING_WEBHOOK_ENABLED', false),
        ],
        'recipients' => [
            'admin' => env('MONITORING_ADMIN_EMAIL'),
            'developers' => env('MONITORING_DEVELOPERS_EMAIL'),
        ],
        'webhook_url' => env('MONITORING_WEBHOOK_URL'),
        'slack_webhook' => env('MONITORING_SLACK_WEBHOOK'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Metrics Collection
    |--------------------------------------------------------------------------
    */
    'metrics' => [
        'enabled' => true,
        'storage' => 'redis', // redis, database, file
        'retention' => [
            'hourly' => 24, // hours
            'daily' => 30, // days
            'monthly' => 12, // months
        ],
        'collection_interval' => 60, // seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Alerting Rules
    |--------------------------------------------------------------------------
    */
    'alerts' => [
        'response_time' => [
            'warning' => 500, // ms
            'critical' => 2000, // ms
        ],
        'error_rate' => [
            'warning' => 0.05, // 5%
            'critical' => 0.1, // 10%
        ],
        'memory_usage' => [
            'warning' => 0.8, // 80%
            'critical' => 0.95, // 95%
        ],
        'disk_usage' => [
            'warning' => 0.8, // 80%
            'critical' => 0.9, // 90%
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode
    |--------------------------------------------------------------------------
    */
    'maintenance' => [
        'enabled' => true,
        'allowed_ips' => [
            '127.0.0.1',
            '::1',
        ],
        'bypass_token' => env('MAINTENANCE_BYPASS_TOKEN'),
    ],
];
