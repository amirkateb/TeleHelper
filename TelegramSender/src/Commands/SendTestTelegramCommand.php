<?php

namespace TeleHelper\TelegramSender\Commands;

use Illuminate\Console\Command;
use TeleHelper\TelegramSender\TelegramSender;

class SendTestTelegramCommand extends Command
{
    protected $signature = 'telegram:send 
                            {chat_id : The Telegram chat ID} 
                            {message : The message content} 
                            {--bot= : Optional bot name (defined in config)}';

    protected $description = 'Send a test message to a specific chat using a configured Telegram bot.';

    public function handle(): int
    {
        $chatId  = $this->argument('chat_id');
        $message = $this->argument('message');
        $botName = $this->option('bot') ?? config('telegram-sender.default_bot');

        $sender = app(TelegramSender::class);

        if (! $sender->hasBot($botName)) {
            $this->error("❌ Bot '{$botName}' is not defined in your configuration.");
            return self::FAILURE;
        }

        try {
            $sender->bot($botName)->sendMessage($chatId, $message);
            $this->info("✅ Message successfully sent to chat ID {$chatId} using bot '{$botName}'.");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("❌ Failed to send message: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}