<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use TeleHelper\TelegramSender\Telegram;

class SendTelegramMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $botName;
    protected string|int $chatId;
    protected string $text;
    protected array $options;

    /**
     * Create a new job instance.
     */
    public function __construct(string $botName, string|int $chatId, string $text, array $options = [])
    {
        $this->botName = $botName;
        $this->chatId = $chatId;
        $this->text = $text;
        $this->options = $options;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Telegram::bot($this->botName)->sendMessage(
            $this->chatId,
            $this->text,
            $this->options
        );
    }
}