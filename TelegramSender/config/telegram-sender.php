<?php

return [

    'default_bot' => 'default',

    'bots' => [
        [
            'name' => 'default',
            'token' => env('TELEGRAM_BOT_TOKEN'),
        ],
        [
            'name' => 'marketing',
            'token' => env('TELEGRAM_MARKETING_TOKEN'),
        ],
        [
            'name' => 'support',
            'token' => env('TELEGRAM_SUPPORT_TOKEN'),
        ],
    ],

];