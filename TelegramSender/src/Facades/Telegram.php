<?php

namespace TeleHelper\TelegramSender\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \TeleHelper\TelegramSender\TelegramSender
 */
class Telegram extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \TeleHelper\TelegramSender\TelegramSender::class;
    }
}