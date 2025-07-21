<?php

namespace TeleHelper\TelegramSender\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use TeleHelper\TelegramSender\TelegramSender;

class SendTelegramJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $botName;
    public string $method;
    public array $arguments;

    public function __construct(string $botName, string $method, array $arguments)
    {
        $this->botName = $botName;
        $this->method = $method;
        $this->arguments = $arguments;
    }

    public function handle(): void
    {
        $bot = app(TelegramSender::class)->bot($this->botName);
        call_user_func_array([$bot, $this->method], $this->arguments);
    }
}