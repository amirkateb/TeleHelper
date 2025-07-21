<?php

namespace TeleHelper\TelegramSender\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use TeleHelper\TelegramSender\TelegramSenderServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            TelegramSenderServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('telegram-sender.bots', [
            [
                'name' => 'default',
                'token' => 'fake-token'
            ],
        ]);
    }
}