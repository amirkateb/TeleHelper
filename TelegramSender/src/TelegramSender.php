<?php
namespace TeleHelper\TelegramSender;

class TelegramSender
{
    protected array $bots;

    public function __construct()
    {
        $this->bots = collect(config('telegram-sender.bots'))
            ->map(fn($bot) => new TelegramBot($bot['token'], $bot['chat_id']))
            ->toArray();
    }

    public function bot(string $name = null): TelegramBot
    {
        $name = $name ?? config('telegram-sender.default_bot');

        if (!isset($this->bots[$name])) {
            throw new TelegramException("Bot '{$name}' not found in config.");
        }

        return $this->bots[$name];
    }
}