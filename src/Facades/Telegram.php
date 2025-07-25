<?php

namespace TeleHelper\TelegramSender\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for TelegramSender
 *
 * @method static \TeleHelper\TelegramSender\TelegramBot bot(string $name = null)
 * @method static array listBots()
 * @method static bool hasBot(string $name)
 * @method static void addBot(string $name, string $token)
 *
 * @see \TeleHelper\TelegramSender\TelegramSender
 */
class Telegram extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \TeleHelper\TelegramSender\TelegramSender::class;
    }
}