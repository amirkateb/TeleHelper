<?php
namespace TeleHelper\TelegramSender;

use Illuminate\Support\Facades\Http;

class TelegramBot
{
    protected string $token;
    protected string $chatId;

    public function __construct(string $token, string $chatId)
    {
        $this->token = $token;
        $this->chatId = $chatId;
    }

    protected function send(string $method, array $params): array
    {
        $url = "https://api.telegram.org/bot{$this->token}/{$method}";

        try {
            $response = Http::timeout(10)->post($url, $params);
            if (!$response->successful()) {
                throw new TelegramException("Telegram API Error: " . $response->body());
            }
            return $response->json();
        } catch (\Throwable $e) {
            throw new TelegramException("Send failed: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function sendMessage(string $text, array $buttons = []): array
    {
        return $this->send('sendMessage', [
            'chat_id' => $this->chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => !empty($buttons) ? json_encode(['inline_keyboard' => $buttons]) : null,
        ]);
    }

    public function sendPhoto(string $photoUrl, ?string $caption = null): array
    {
        return $this->send('sendPhoto', [
            'chat_id' => $this->chatId,
            'photo' => $photoUrl,
            'caption' => $caption,
        ]);
    }

    public function sendDocument(string $fileUrl, ?string $caption = null): array
    {
        return $this->send('sendDocument', [
            'chat_id' => $this->chatId,
            'document' => $fileUrl,
            'caption' => $caption,
        ]);
    }

    // ادامه: sendMediaGroup, editMessage, deleteMessage ...
}