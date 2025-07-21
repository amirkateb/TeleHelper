<?php
return [
    'bots' => [
        'default' => [
            'token' => env('TELEGRAM_BOT_TOKEN'),
            'chat_id' => env('TELEGRAM_CHAT_ID'),
        ],
        'admin' => [
            'token' => env('TELEGRAM_ADMIN_BOT_TOKEN'),
            'chat_id' => env('TELEGRAM_ADMIN_CHAT_ID'),
        ],
    ],
    'default_bot' => 'default',
];