<?php

namespace TeleHelper\TelegramSender;

use Illuminate\Support\ServiceProvider;
use TeleHelper\TelegramSender\Commands\SendTestTelegramCommand;

class TelegramSenderServiceProvider extends ServiceProvider
{
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

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/telegram-sender.php' => config_path('telegram-sender.php'),
        ], 'telegram-sender-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                SendTestTelegramCommand::class,
            ]);
        }
    }
}