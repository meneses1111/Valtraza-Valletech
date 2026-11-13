<?php

return [
    'default' => env('DB_CONNECTION', 'pgsql'),
    'connections' => [
        'pgsql' => [
            'driver' => 'pgsql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'valtraza'),
            'username' => env('DB_USERNAME', 'postgres'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => env('DB_SSLMODE', 'prefer'),
            'timezone' => 'UTC',
            'options' => [
                PDO::ATTR_TIMEOUT => max(1, (int) env('DB_CONNECT_TIMEOUT', 5)),
                PDO::ATTR_PERSISTENT => (bool) env('DB_PERSISTENT', false),
            ],
            'server_options' => [
                'statement_timeout' => max(1, (int) env('DB_STATEMENT_TIMEOUT', 5000)),
                'lock_timeout' => max(1, (int) env('DB_LOCK_TIMEOUT', 3000)),
                'idle_in_transaction_session_timeout' => 15000,
            ],
        ],
    ],
    'migrations' => ['table' => 'migrations', 'update_date_on_publish' => true],
];
