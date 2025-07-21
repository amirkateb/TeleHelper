<?php

namespace TeleHelper\TelegramSender;
use TeleHelper\TelegramSender\Jobs\SendTelegramJob;

class TelegramSender
{
    /**
     * @var TelegramBot[]
     */
    protected array $bots = [];

    public function __construct()
    {
        $this->loadBotsFromConfig();
    }

    protected function loadBotsFromConfig(): void
    {
        $configuredBots = config('telegram-sender.bots', []);

        foreach ($configuredBots as $bot) {
            if (!isset($bot['name'], $bot['token'])) {
                continue;
            }

            $this->addBot($bot['name'], $bot['token']);
        }
    }

    public function addBot(string $name, string $token): void
    {
        $this->bots[$name] = new TelegramBot($token);
    }

    public function bot(string $name = null): TelegramBot
    {
        $name = $name ?? config('telegram-sender.default_bot');

        if (!isset($this->bots[$name])) {
            throw new TelegramException("Bot '{$name}' not found in config.");
        }

        return $this->bots[$name];
    }

    public function hasBot(string $name): bool
    {
        return isset($this->bots[$name]);
    }

    public function listBots(): array
    {
        return array_keys($this->bots);
    }
    
    public function sendQueued(string $botName, string $method, array $arguments): void
{
    SendTelegramJob::dispatch($botName, $method, $arguments);
}
}