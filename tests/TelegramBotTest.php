<?php

namespace TeleHelper\TelegramSender\Tests;

use Illuminate\Support\Facades\Http;
use TeleHelper\TelegramSender\TelegramBot;

class TelegramBotTest extends TestCase
{
    /** @test */
    public function it_sends_message_successfully()
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response(['ok' => true, 'result' => []]),
        ]);

        $bot = new TelegramBot('fake-token');
        $result = $bot->sendMessage('123456', 'hello test');

        $this->assertTrue($result['ok']);
    }

    /** @test */
    public function it_handles_failed_response()
    {
        Http::fake([
            'https://api.telegram.org/*' => Http::response('Unauthorized', 401),
        ]);

        $this->expectException(\TeleHelper\TelegramSender\TelegramException::class);

        $bot = new TelegramBot('fake-token');
        $bot->sendMessage('123456', 'test fail');
    }
}