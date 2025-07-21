<?php

namespace TeleHelper\TelegramSender;

use Illuminate\Support\Facades\Http;

class TelegramBot
{
    protected string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    protected function send(string $method, array $params): array
{
    $url = "https://api.telegram.org/bot{$this->token}/{$method}";

    try {
        $http = Http::timeout(10);
        if (config('telegram-sender.proxy.enabled')) {
            $proxyUrl = config('telegram-sender.proxy.type') . '://' .
                        config('telegram-sender.proxy.host') . ':' .
                        config('telegram-sender.proxy.port');

            $http = $http->withOptions([
                'proxy' => $proxyUrl,
                'auth' => config('telegram-sender.proxy.username')
                    ? [config('telegram-sender.proxy.username'), config('telegram-sender.proxy.password')]
                    : null
            ]);
        }

        $response = $http->post($url, $params);

        if (!$response->successful()) {
            throw new TelegramException("Telegram API error ({$method}): " . $response->body());
        }

        return $response->json();
    } catch (\Throwable $e) {
        throw new TelegramException("Send failed ({$method}): " . $e->getMessage(), $e->getCode(), $e);
    }
}

    public function sendMessage(string $chatId, string $text, array $options = []): array
    {
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $options['parse_mode'] ?? 'HTML',
            'disable_web_page_preview' => $options['disable_web_page_preview'] ?? false,
            'disable_notification' => $options['disable_notification'] ?? false,
            'reply_to_message_id' => $options['reply_to_message_id'] ?? null,
        ];

        if (!empty($options['buttons'])) {
            $payload['reply_markup'] = json_encode([
                'inline_keyboard' => $options['buttons'],
            ]);
        }

        return $this->send('sendMessage', $payload);
    }

    public function sendPhoto(string $chatId, string $photo, ?string $caption = null, array $options = []): array
    {
        $payload = [
            'chat_id' => $chatId,
            'photo' => $photo,
            'caption' => $caption,
            'parse_mode' => $options['parse_mode'] ?? 'HTML',
            'disable_notification' => $options['disable_notification'] ?? false,
            'reply_to_message_id' => $options['reply_to_message_id'] ?? null,
        ];

        if (!empty($options['buttons'])) {
            $payload['reply_markup'] = json_encode([
                'inline_keyboard' => $options['buttons'],
            ]);
        }

        return $this->send('sendPhoto', $payload);
    }

    public function sendDocument(string $chatId, string $fileUrl, ?string $caption = null, array $options = []): array
    {
        $payload = [
            'chat_id' => $chatId,
            'document' => $fileUrl,
            'caption' => $caption,
            'parse_mode' => $options['parse_mode'] ?? 'HTML',
            'disable_notification' => $options['disable_notification'] ?? false,
            'reply_to_message_id' => $options['reply_to_message_id'] ?? null,
        ];

        if (!empty($options['buttons'])) {
            $payload['reply_markup'] = json_encode([
                'inline_keyboard' => $options['buttons'],
            ]);
        }

        return $this->send('sendDocument', $payload);
    }

    public function sendMediaGroup(string $chatId, array $media): array
    {
        $payload = [
            'chat_id' => $chatId,
            'media' => json_encode($media),
        ];

        return $this->send('sendMediaGroup', $payload);
    }

    public function editMessageText(string $chatId, int $messageId, string $newText, array $options = []): array
    {
        $payload = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $newText,
            'parse_mode' => $options['parse_mode'] ?? 'HTML',
        ];

        if (!empty($options['buttons'])) {
            $payload['reply_markup'] = json_encode([
                'inline_keyboard' => $options['buttons'],
            ]);
        }

        return $this->send('editMessageText', $payload);
    }

    public function deleteMessage(string $chatId, int $messageId): array
    {
        return $this->send('deleteMessage', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ]);
    }

    public function answerCallbackQuery(string $callbackQueryId, string $text = '', bool $showAlert = false): array
    {
        return $this->send('answerCallbackQuery', [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
            'show_alert' => $showAlert,
        ]);
    }

    public function sendReplyKeyboard(string $chatId, string $text, array $keyboard, array $options = []): array
    {
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $options['parse_mode'] ?? 'HTML',
            'reply_markup' => json_encode([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => $options['one_time_keyboard'] ?? false,
            ]),
        ];

        return $this->send('sendMessage', $payload);
    }

    public function sendForceReply(string $chatId, string $text, array $options = []): array
    {
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $options['parse_mode'] ?? 'HTML',
            'reply_markup' => json_encode([
                'force_reply' => true,
                'selective' => $options['selective'] ?? false,
            ]),
        ];

        return $this->send('sendMessage', $payload);
    }

    public function removeKeyboard(string $chatId, string $text, array $options = []): array
    {
        $payload = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $options['parse_mode'] ?? 'HTML',
            'reply_markup' => json_encode([
                'remove_keyboard' => true,
            ]),
        ];

        return $this->send('sendMessage', $payload);
    }

    public function sendLocation(string $chatId, float $latitude, float $longitude, array $options = []): array
    {
        $payload = [
            'chat_id' => $chatId,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'disable_notification' => $options['disable_notification'] ?? false,
            'horizontal_accuracy' => $options['accuracy'] ?? null,
        ];

        return $this->send('sendLocation', $payload);
    }

    public function sendChatAction(string $chatId, string $action): array
    {
        return $this->send('sendChatAction', [
            'chat_id' => $chatId,
            'action' => $action,
        ]);
    }
    
    public function sendPhotoFile(
    string $filePath,
    ?string $caption = null,
    array $options = []
): array {
    $url = "https://api.telegram.org/bot{$this->token}/sendPhoto";

    try {
        $response = Http::timeout(15)
            ->withOptions([
                'proxy' => config('telegram-sender.proxy.enabled') ? sprintf(
                    '%s://%s%s',
                    config('telegram-sender.proxy.type'),
                    config('telegram-sender.proxy.username')
                        ? config('telegram-sender.proxy.username') . ':' . config('telegram-sender.proxy.password') . '@'
                        : '',
                    config('telegram-sender.proxy.host') . ':' . config('telegram-sender.proxy.port')
                ) : null,
            ])
            ->attach('photo', fopen($filePath, 'r'), basename($filePath))
            ->post($url, [
                'chat_id' => $this->chatId,
                'caption' => $caption,
                'parse_mode' => $options['parse_mode'] ?? 'HTML',
                'disable_notification' => $options['disable_notification'] ?? false,
                'reply_to_message_id' => $options['reply_to_message_id'] ?? null,
                'reply_markup' => !empty($options['buttons']) ? json_encode([
                    'inline_keyboard' => $options['buttons'],
                ]) : null,
            ]);

        if (!$response->successful()) {
            throw new TelegramException("Telegram API error (sendPhotoFile): " . $response->body());
        }

        return $response->json();
    } catch (\Throwable $e) {
        throw new TelegramException("sendPhotoFile failed: " . $e->getMessage(), $e->getCode(), $e);
    }
}

public function sendDocumentFile(
    string $filePath,
    ?string $caption = null,
    array $options = []
): array {
    $url = "https://api.telegram.org/bot{$this->token}/sendDocument";

    try {
        $response = Http::timeout(15)
            ->withOptions([
                'proxy' => config('telegram-sender.proxy.enabled') ? sprintf(
                    '%s://%s%s',
                    config('telegram-sender.proxy.type'),
                    config('telegram-sender.proxy.username')
                        ? config('telegram-sender.proxy.username') . ':' . config('telegram-sender.proxy.password') . '@'
                        : '',
                    config('telegram-sender.proxy.host') . ':' . config('telegram-sender.proxy.port')
                ) : null,
            ])
            ->attach('document', fopen($filePath, 'r'), basename($filePath))
            ->post($url, [
                'chat_id' => $this->chatId,
                'caption' => $caption,
                'parse_mode' => $options['parse_mode'] ?? 'HTML',
                'disable_notification' => $options['disable_notification'] ?? false,
                'reply_to_message_id' => $options['reply_to_message_id'] ?? null,
                'reply_markup' => !empty($options['buttons']) ? json_encode([
                    'inline_keyboard' => $options['buttons'],
                ]) : null,
            ]);

        if (!$response->successful()) {
            throw new TelegramException("Telegram API error (sendDocumentFile): " . $response->body());
        }

        return $response->json();
    } catch (\Throwable $e) {
        throw new TelegramException("sendDocumentFile failed: " . $e->getMessage(), $e->getCode(), $e);
    }
}
}