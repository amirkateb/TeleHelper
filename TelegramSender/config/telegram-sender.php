<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Bot Name
    |--------------------------------------------------------------------------
    |
    | The default bot used when no name is specified in Telegram::bot().
    |
    */
    'default_bot' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Telegram Bots
    |--------------------------------------------------------------------------
    |
    | Define your bot name and token here. You can add more bots if needed.
    | Each bot must have a unique 'name' and a valid Telegram 'token'.
    |
    */
    'bots' => [
        [
            'name' => 'default',
            'token' => env('TELEGRAM_BOT_TOKEN'),
        ],

        // Example: Add more bots like this
        /*
        [
            'name' => 'support',
            'token' => env('TELEGRAM_SUPPORT_TOKEN'),
        ],
        [
            'name' => 'marketing',
            'token' => env('TELEGRAM_MARKETING_TOKEN'),
        ],
        */
    ],

    /*
    |--------------------------------------------------------------------------
    | Proxy Settings (Optional)
    |--------------------------------------------------------------------------
    |
    | If your server requires Telegram requests to go through a proxy
    | (e.g., HTTP, SOCKS5), enable and configure the settings below.
    |
    */
    'proxy' => [
        'enabled'  => env('TELEGRAM_PROXY_ENABLED', false),
        'host'     => env('TELEGRAM_PROXY_HOST', null),
        'port'     => env('TELEGRAM_PROXY_PORT', null),
        'username' => env('TELEGRAM_PROXY_USERNAME', null),
        'password' => env('TELEGRAM_PROXY_PASSWORD', null),
        'type'     => env('TELEGRAM_PROXY_TYPE', 'http'), // http, socks5, socks4
    ],

];