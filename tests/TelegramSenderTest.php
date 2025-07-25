<?php

namespace TeleHelper\TelegramSender\Tests;

use TeleHelper\TelegramSender\TelegramSender;

class TelegramSenderTest extends TestCase
{
    /** @test */
    public function it_loads_configured_bots()
    {
        $sender = app(TelegramSender::class);

        $this->assertTrue($sender->hasBot('default'));
        $this->assertInstanceOf(
            \TeleHelper\TelegramSender\TelegramBot::class,
            $sender->bot('default')
        );
    }

    /** @test */
    public function it_throws_exception_if_bot_not_found()
    {
        $this->expectException(\TeleHelper\TelegramSender\TelegramException::class);

        app(TelegramSender::class)->bot('nonexistent');
    }
}