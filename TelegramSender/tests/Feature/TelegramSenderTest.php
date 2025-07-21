<?php

namespace TeleHelper\TelegramSender\Tests\Feature;

use Orchestra\Testbench\TestCase;
use TeleHelper\TelegramSender\Facades\Telegram;
use TeleHelper\TelegramSender\TelegramSenderServiceProvider;

class TelegramSenderTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            TelegramSenderServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Telegram' => Telegram::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('telegram-sender.bots', [
            [
                'name' => 'default',
                'token' => env('TELEGRAM_BOT_TOKEN_TEST', 'YOUR_TEST_BOT_TOKEN'),
                'chat_id' => env('TELEGRAM_CHAT_ID_TEST', 'YOUR_TEST_CHAT_ID'),
            ],
        ]);
        $app['config']->set('telegram-sender.default_bot', 'default');
    }

    /** @test */
    public function it_sends_text_message()
    {
        $result = Telegram::bot()->sendMessage('✅ تست ارسال پیام از پکیج');
        $this->assertTrue($result['ok'] ?? false);
    }

    /** @test */
    public function it_sends_photo_by_url()
    {
        $result = Telegram::bot()->sendPhoto(
            'https://upload.wikimedia.org/wikipedia/commons/9/9a/Gull_portrait_ca_usa.jpg',
            '🖼️ تست ارسال تصویر از پکیج'
        );
        $this->assertTrue($result['ok'] ?? false);
    }

    /** @test */
    public function it_sends_document_by_url()
    {
        $result = Telegram::bot()->sendDocument(
            'https://file-examples.com/wp-content/uploads/2017/02/file-sample_100kB.doc',
            '📄 تست سند'
        );
        $this->assertTrue($result['ok'] ?? false);
    }
}