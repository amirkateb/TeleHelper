<?php

namespace TeleHelper\TelegramSender\Commands;

use Illuminate\Console\Command;
use TeleHelper\TelegramSender\TelegramSender;

class SendTestTelegramCommand extends Command
{
    protected $signature = 'telegram:test
                            {bot : Name of the bot (defined in config)}
                            {chat_id : Chat ID to send the message to}
                            {message : The message content}';

    protected $description = 'Send a test message using a selected Telegram bot';

    public function handle()
    {
        $botName = $this->argument('bot');
        $chatId = $this->argument('chat_id');
        $message = $this->argument('message');

        try {
            $sender = app(TelegramSender::class);
            if (! $sender->hasBot($botName)) {
                $this->error("❌ Bot '{$botName}' not found.");
                return Command::FAILURE;
            }

            $sender->bot($botName)->sendMessage($chatId, $message);
            $this->info("✅ Message sent successfully using bot '{$botName}'");

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("❌ Failed to send message: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}