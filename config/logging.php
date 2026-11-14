<?php

return [
    'default' => env('LOG_CHANNEL', 'daily'),
    'channels' => [
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/valtraza.log'),
            'level' => env('LOG_LEVEL', 'warning'),
            'days' => 14,
            'locking' => true,
            'replace_placeholders' => true,
        ],
    ],
];
