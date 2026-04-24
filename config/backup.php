<?php

return [

    'backup' => [

        /*
         * The name of this application. You can use this name to monitor
         * the backups.
         */
        'name' => env('BACKUP_NAME', ''),

        'source' => [

            'files' => [

                /*
                 * The list of directories and files that will be included in the backup.
                 */
                'include' => [
                    base_path(),
                ],

                /*
                 * These directories and files will be excluded from the backup.
                 */
                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                ],

                /*
                 * Determines if symbols links should be followed.
                 */
                'follow_links' => false,

                /*
                 * Determines if it should avoid soaring into other file systems
                 * across device boundaries.
                 */
                'ignore_unreadable_directories' => false,

                /*
                 * The maximum age of backup files that should be kept.
                 */
                'relative_path' => null,
            ],

            /*
             * The names of the connections to the databases that should be backed up.
             * MySQL, PostgreSQL, SQLite and Mongo are supported.
             *
             * The `db_dump` path is relevant only for MySQL and PostgreSQL.
             */
            'databases' => [
                'mysql',
            ],
        ],

        /*
         * The database dump can be customized by setting these options.
         * For more information on how to customize the dump, visit the db-dumper repository.
         *
         * https://github.com/spatie/db-dumper
         */
        'database_dump_compressor' => null,

        /*
         * The compressor that should be used for the backup.
         *
         * Available compressors:
         * - Spatie\Backup\Tasks\Backup\ZipCompressor
         * - Spatie\Backup\Tasks\Backup\TarCompressor
         * - Spatie\Backup\Tasks\Backup\TarGzCompressor
         */
        'database_dump_file_extension' => '',

        'destination' => [

            /*
             * The filename prefix used for the backup zip file.
             */
            'filename_prefix' => '',

            /*
             * The disk names on which the backups will be stored.
             */
            'disks' => [
                'google',
            ],
        ],

        /*
         * The directory where the temporary files will be stored.
         */
        'temporary_directory' => storage_path('app/backup-temp'),

        /*
         * The password that will be used to encrypt the backup zip file.
         *
         * Set to null to disable encryption.
         */
        'password' => env('BACKUP_ARCHIVE_PASSWORD'),

        /*
         * The encryption algorithm that should be used to encrypt the backup zip file.
         */
        'encryption' => 'default',
    ],

    /*
     * You can get notified when specific events occur. Out of the box, only email and slack
     * notifications are supported.
     */
    'notifications' => [

        'notifications' => [
            \Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification::class => ['mail'],
        ],

        /*
         * Here you can specify the notifiable to which the notifications should be sent.
         */
        'notifiable' => \Spatie\Backup\Notifications\Notifiable::class,

        'mail' => [
            'to' => env('BACKUP_NOTIFICATION_EMAIL', 'info@emca.co.tz'),

            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                'name' => env('MAIL_FROM_NAME', 'Example'),
            ],
        ],

        'slack' => [
            'webhook_url' => '',

            /*
             * If this is set to null the default channel of the webhook will be used.
             */
            'channel' => null,

            'username' => null,

            'icon' => null,
        ],

        'discord' => [
            'webhook_url' => '',

            /*
             * If this is set to null the default channel of the webhook will be used.
             */
            'username' => null,

            'avatar_url' => null,
        ],
    ],

    /*
     * Here you can specify which backups should be kept.
     */
    'monitor_backups' => [
        [
            'name' => env('BACKUP_NAME', ''),
            'disks' => ['google'],
            'health_checks' => [
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 1,
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 5000,
            ],
        ],
    ],

    'cleanup' => [
        /*
         * The strategy that will be used to cleanup old backups. The default strategy
         * will keep all backups for a certain amount of days. After that period only
         * a daily backup will be kept. After that period only weekly backups will
         * be kept and so on.
         *
         * No matter how many backups are removed, the last backup will always be kept.
         */
        'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,

        'default_strategy' => [

            /*
             * The number of days for which all backups must be kept.
             */
            'keep_all_backups_for_days' => 7,

            /*
             * The number of days for which daily backups must be kept.
             */
            'keep_daily_backups_for_days' => 16,

            /*
             * The number of weeks for which one weekly backup must be kept.
             */
            'keep_weekly_backups_for_weeks' => 8,

            /*
             * The number of months for which one monthly backup must be kept.
             */
            'keep_monthly_backups_for_months' => 4,

            /*
             * The number of years for which one yearly backup must be kept.
             */
            'keep_yearly_backups_for_years' => 2,

            /*
             * After cleaning up the backups remove the oldest backup until
             * this amount of megabytes has been reached.
             */
            'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
        ],
    ],

];
