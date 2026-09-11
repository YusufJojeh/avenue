<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Backup Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for automated backups,
    | disaster recovery, and data protection.
    |
    */

    'enabled' => env('BACKUP_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Backup Schedule
    |--------------------------------------------------------------------------
    */
    'schedule' => [
        'database' => [
            'enabled' => true,
            'frequency' => 'daily', // daily, weekly, monthly
            'time' => '02:00', // 2 AM
            'retention' => [
                'daily' => 7, // Keep 7 daily backups
                'weekly' => 4, // Keep 4 weekly backups
                'monthly' => 12, // Keep 12 monthly backups
            ],
        ],
        'files' => [
            'enabled' => true,
            'frequency' => 'weekly',
            'time' => '03:00',
            'retention' => [
                'weekly' => 4,
                'monthly' => 6,
            ],
        ],
        'uploads' => [
            'enabled' => true,
            'frequency' => 'daily',
            'time' => '04:00',
            'retention' => [
                'daily' => 7,
                'weekly' => 4,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    */
    'storage' => [
        'local' => [
            'enabled' => true,
            'path' => storage_path('backups'),
            'max_size' => '10GB',
        ],
        's3' => [
            'enabled' => env('BACKUP_S3_ENABLED', false),
            'bucket' => env('BACKUP_S3_BUCKET'),
            'region' => env('BACKUP_S3_REGION'),
            'path' => env('BACKUP_S3_PATH', 'backups'),
        ],
        'ftp' => [
            'enabled' => env('BACKUP_FTP_ENABLED', false),
            'host' => env('BACKUP_FTP_HOST'),
            'username' => env('BACKUP_FTP_USERNAME'),
            'password' => env('BACKUP_FTP_PASSWORD'),
            'path' => env('BACKUP_FTP_PATH', '/backups'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Backup
    |--------------------------------------------------------------------------
    */
    'database' => [
        'enabled' => true,
        'tables' => [
            'include' => [
                'users',
                'products',
                'categories',
                'brands',
                'orders',
                'settings',
                'translations',
                'content_versions',
            ],
            'exclude' => [
                'migrations',
                'password_resets',
                'failed_jobs',
                'personal_access_tokens',
            ],
        ],
        'options' => [
            'single_transaction' => true,
            'lock_tables' => false,
            'add_drop_table' => true,
            'add_insert' => true,
            'extended_insert' => true,
            'complete_insert' => false,
            'delayed_insert' => false,
            'replace' => false,
            'ignore' => false,
        ],
        'compression' => 'gzip',
        'encryption' => [
            'enabled' => env('BACKUP_ENCRYPTION_ENABLED', false),
            'algorithm' => 'AES-256-CBC',
            'key' => env('BACKUP_ENCRYPTION_KEY'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | File Backup
    |--------------------------------------------------------------------------
    */
    'files' => [
        'enabled' => true,
        'include' => [
            'app/',
            'config/',
            'database/',
            'resources/',
            'routes/',
            'storage/app/public/',
        ],
        'exclude' => [
            'node_modules/',
            'vendor/',
            'storage/logs/',
            'storage/framework/cache/',
            'storage/framework/sessions/',
            'storage/framework/views/',
            '.git/',
            '.env',
        ],
        'compression' => 'zip',
        'max_file_size' => '100MB',
    ],

    /*
    |--------------------------------------------------------------------------
    | Upload Backup
    |--------------------------------------------------------------------------
    */
    'uploads' => [
        'enabled' => true,
        'path' => storage_path('app/public'),
        'include_subdirectories' => true,
        'compression' => 'tar.gz',
        'max_file_size' => '500MB',
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'enabled' => true,
        'channels' => [
            'mail' => env('BACKUP_MAIL_ENABLED', true),
            'slack' => env('BACKUP_SLACK_ENABLED', false),
            'webhook' => env('BACKUP_WEBHOOK_ENABLED', false),
        ],
        'events' => [
            'backup_started' => true,
            'backup_completed' => true,
            'backup_failed' => true,
            'backup_cleaned' => true,
        ],
        'recipients' => [
            'admin' => env('BACKUP_ADMIN_EMAIL'),
            'developers' => env('BACKUP_DEVELOPERS_EMAIL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Recovery Configuration
    |--------------------------------------------------------------------------
    */
    'recovery' => [
        'enabled' => true,
        'test_restore' => [
            'enabled' => env('BACKUP_TEST_RESTORE', false),
            'frequency' => 'weekly',
            'environment' => 'staging',
        ],
        'point_in_time' => [
            'enabled' => true,
            'retention' => '30 days',
        ],
        'automated_recovery' => [
            'enabled' => false,
            'max_attempts' => 3,
            'timeout' => 3600, // 1 hour
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring & Health Checks
    |--------------------------------------------------------------------------
    */
    'monitoring' => [
        'enabled' => true,
        'checks' => [
            'backup_size' => true,
            'backup_age' => true,
            'backup_integrity' => true,
            'storage_space' => true,
            'backup_success_rate' => true,
        ],
        'thresholds' => [
            'max_backup_age_hours' => 48,
            'min_backup_size_mb' => 1,
            'max_storage_usage_percent' => 90,
            'min_success_rate_percent' => 95,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */
    'security' => [
        'encryption' => [
            'enabled' => env('BACKUP_ENCRYPTION_ENABLED', false),
            'algorithm' => 'AES-256-CBC',
        ],
        'access_control' => [
            'ip_whitelist' => [
                '127.0.0.1',
                '::1',
            ],
            'authentication' => true,
            'api_key' => env('BACKUP_API_KEY'),
        ],
        'audit_logging' => [
            'enabled' => true,
            'retention' => '1 year',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    */
    'performance' => [
        'parallel_backups' => [
            'enabled' => false,
            'max_concurrent' => 2,
        ],
        'compression_level' => 6, // 1-9, higher = more compression but slower
        'chunk_size' => '100MB',
        'timeout' => [
            'database_backup' => 3600, // 1 hour
            'file_backup' => 7200, // 2 hours
            'upload_backup' => 10800, // 3 hours
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cleanup Settings
    |--------------------------------------------------------------------------
    */
    'cleanup' => [
        'enabled' => true,
        'schedule' => 'daily',
        'time' => '05:00',
        'remove_old_backups' => true,
        'remove_failed_backups' => true,
        'remove_corrupted_backups' => true,
        'log_cleanup_actions' => true,
    ],
];
