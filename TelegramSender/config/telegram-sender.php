<?php

return [

    'default_bot' => 'default',

    'bots' => [
        [
            'name' => 'default',
            'token' => env('TELEGRAM_BOT_TOKEN'),
        ],
        // Add more bots here...
    ],

];