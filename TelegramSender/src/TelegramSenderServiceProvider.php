<?php

namespace TeleHelper\TelegramSender;

use Illuminate\Support\ServiceProvider;

class TelegramSenderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

        $this->mergeConfigFrom(
            __DIR__ . '/../config/telegram-sender.php',
            'telegram-sender'
        );

        $this->app->singleton(TelegramSender::class, function () {
            return new TelegramSender();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/telegram-sender.php' => config_path('telegram-sender.php'),
        ], 'telegram-sender-config');
    }
}