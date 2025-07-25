<?php

namespace TeleHelper\TelegramSender\Commands;

use Illuminate\Console\Command;
use TeleHelper\TelegramSender\TelegramSender;

class SendTestTelegramCommand extends Command
{
    protected $signature = 'telegram:send 
        {chat_id : Telegram chat ID} 
        {--bot= : Bot name (default is config default)} 
        {--text= : Text message to send} 
        {--photo= : URL or path of photo to send} 
        {--document= : URL or path of document to send} 
        {--caption= : Caption for media}
        {--buttons= : JSON string for inline keyboard}
        {--media-group= : JSON array of media items}
        {--no-preview : Disable link preview}
        {--no-notify : Disable notification}';

    protected $description = 'Send various types of Telegram messages using a configured bot.';

    public function handle(): int
    {
        $botName = $this->option('bot') ?? config('telegram-sender.default_bot');
        $chatId  = $this->argument('chat_id');

        $sender = app(TelegramSender::class);

        if (! $sender->hasBot($botName)) {
            $this->error("❌ Bot '{$botName}' not found.");
            return self::FAILURE;
        }

        $bot = $sender->bot($botName);

        $options = [
            'disable_web_page_preview' => $this->option('no-preview'),
            'disable_notification'     => $this->option('no-notify'),
        ];

        if ($this->option('buttons')) {
            $decoded = json_decode($this->option('buttons'), true);
            if (!is_array($decoded)) {
                $this->error('❌ Invalid buttons JSON.');
                return self::FAILURE;
            }
            $options['buttons'] = $decoded;
        }

        try {
            if ($text = $this->option('text')) {
                $bot->sendMessage($chatId, $text, $options);
                $this->info("✅ Text message sent.");
            }

            if ($photo = $this->option('photo')) {
                $bot->sendPhoto($chatId, $photo, $this->option('caption'), $options);
                $this->info("✅ Photo sent.");
            }

            if ($doc = $this->option('document')) {
                $bot->sendDocument($chatId, $doc, $this->option('caption'), $options);
                $this->info("✅ Document sent.");
            }

            if ($mediaJson = $this->option('media-group')) {
                $media = json_decode($mediaJson, true);
                if (!is_array($media)) {
                    $this->error('❌ Invalid media group JSON.');
                    return self::FAILURE;
                }
                $bot->sendMediaGroup($chatId, $media);
                $this->info("✅ Media group sent.");
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("❌ Failed: " . $e->getMessage());
            return self::FAILURE;
        }
    }
}