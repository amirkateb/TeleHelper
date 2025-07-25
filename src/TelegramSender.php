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
    
    public function sendQueuedOn(string $queue, string $botName, string $method, array $arguments): void
{
    SendTelegramJob::dispatch($botName, $method, $arguments)->onQueue($queue);
}

public function sendBulkMessage(array $chatIds, string $message, array $options = [], ?string $queue = null): void
{
    foreach ($chatIds as $chatId) {
        $job = new SendTelegramJob(
            botName: $this->botName,
            method: 'sendMessage',
            payload: [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => $options['parse_mode'] ?? 'HTML',
                'disable_web_page_preview' => $options['disable_web_page_preview'] ?? false,
                'disable_notification' => $options['disable_notification'] ?? false,
                'reply_to_message_id' => $options['reply_to_message_id'] ?? null,
                'reply_markup' => isset($options['buttons']) ? json_encode([
                    'inline_keyboard' => $options['buttons'],
                ]) : null,
            ]
        );

        if ($queue) {
            $job->onQueue($queue);
        }

        dispatch($job);
    }


}