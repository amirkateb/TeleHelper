<?php
namespace TeleHelper\TelegramSender;

use Illuminate\Support\ServiceProvider;

class TelegramSenderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/telegram-sender.php' => config_path('telegram-sender.php'),
        ], 'telegram-sender-config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/telegram-sender.php', 'telegram-sender');

        $this->app->singleton(TelegramSender::class, function () {
            return new TelegramSender();
        });
    }
}