<?php

namespace TeleHelper\TelegramSender;

use TeleHelper\TelegramSender\Jobs\SendTelegramMessageJob;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBot
{
    protected string $token;
    protected ?string $botName;

    public function __construct(string $token, ?string $botName = null)
    {
        $this->token = $token;
        $this->botName = $botName;
    }

    protected function getBaseUrl(string $method): string
    {
        return "https://api.telegram.org/bot{$this->token}/{$method}";
    }

    protected function buildHttpClient()
    {
        $http = Http::timeout(10);

        if (config('telegram-sender.proxy.enabled')) {
            $proxy = config('telegram-sender.proxy');
            $proxyUrl = "{$proxy['type']}://";

            if ($proxy['username']) {
                $proxyUrl .= "{$proxy['username']}:{$proxy['password']}@";
            }

            $proxyUrl .= "{$proxy['host']}:{$proxy['port']}";
            $http = $http->withOptions(['proxy' => $proxyUrl]);
        }

        return $http;
    }

    protected function logMessage(string $method, array $payload): void
    {
        if (config('telegram-sender.log_enabled', false)) {
            Log::channel(config('telegram-sender.log_channel', 'stack'))
                ->info("Telegram ({$method})", [
                    'bot' => $this->botName,
                    'payload' => $payload,
                ]);
        }
    }

    protected function send(string $method, array $payload): array
    {
        $this->logMessage($method, $payload);

        try {
            $response = $this->buildHttpClient()->post($this->getBaseUrl($method), $payload);

            if (!$response->successful()) {
                throw new TelegramException("Telegram API error ({$method}): " . $response->body());
            }

            return $response->json();
        } catch (\Throwable $e) {
            throw new TelegramException("Send failed ({$method}): " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function sendMessage(array|string $chatIds, string $text, array $options = []): array
    {
        $results = [];
        foreach ((array)$chatIds as $chatId) {
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

            $results[] = $this->send('sendMessage', $payload);
        }

        return count($results) === 1 ? $results[0] : $results;
    }

    public function sendPoll(string $chatId, string $question, array $optionsList, array $options = []): array
    {
        $payload = [
            'chat_id' => $chatId,
            'question' => $question,
            'options' => json_encode($optionsList),
            'is_anonymous' => $options['is_anonymous'] ?? true,
            'type' => $options['type'] ?? 'regular',
            'allows_multiple_answers' => $options['multiple'] ?? false,
        ];

        return $this->send('sendPoll', $payload);
    }

    public function sendPhoto(string $chatId, string $urlOrFileId, ?string $caption = null, array $options = []): array
    {
        $payload = [
            'chat_id' => $chatId,
            'photo' => $urlOrFileId,
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

    public function sendPhotoFile(string $chatId, string $filePath, ?string $caption = null, array $options = []): array
    {
        $url = $this->getBaseUrl('sendPhoto');

        $response = $this->buildHttpClient()
            ->attach('photo', fopen($filePath, 'r'), basename($filePath))
            ->post($url, [
                'chat_id' => $chatId,
                'caption' => $caption,
                'parse_mode' => $options['parse_mode'] ?? 'HTML',
                'reply_markup' => !empty($options['buttons']) ? json_encode([
                    'inline_keyboard' => $options['buttons'],
                ]) : null,
            ]);

        if (!$response->successful()) {
            throw new TelegramException("Telegram API error (sendPhotoFile): " . $response->body());
        }

        return $response->json();
    }

    public function sendDocumentFile(string $chatId, string $filePath, ?string $caption = null, array $options = []): array
    {
        $url = $this->getBaseUrl('sendDocument');

        $response = $this->buildHttpClient()
            ->attach('document', fopen($filePath, 'r'), basename($filePath))
            ->post($url, [
                'chat_id' => $chatId,
                'caption' => $caption,
                'parse_mode' => $options['parse_mode'] ?? 'HTML',
                'reply_markup' => !empty($options['buttons']) ? json_encode([
                    'inline_keyboard' => $options['buttons'],
                ]) : null,
            ]);

        if (!$response->successful()) {
            throw new TelegramException("Telegram API error (sendDocumentFile): " . $response->body());
        }

        return $response->json();
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
            'reply_markup' => json_encode(['remove_keyboard' => true]),
        ];

        return $this->send('sendMessage', $payload);
    }

    public function sendChatAction(string $chatId, string $action): array
    {
        return $this->send('sendChatAction', [
            'chat_id' => $chatId,
            'action' => $action,
        ]);
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

    public function answerCallbackQuery(string $callbackQueryId, string $text = '', bool $showAlert = false): array
    {
        return $this->send('answerCallbackQuery', [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
            'show_alert' => $showAlert,
        ]);
    }

    public function deleteMessage(string $chatId, int $messageId): array
    {
        return $this->send('deleteMessage', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ]);
    }

    public function sendLocation(string $chatId, float $latitude, float $longitude, array $options = []): array
    {
        $payload = [
            'chat_id' => $chatId,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'disable_notification' => $options['disable_notification'] ?? false,
            'horizontal_accuracy' => $options['accuracy'] ?? null,
            'live_period' => $options['live_period'] ?? null,
        ];

        return $this->send('sendLocation', $payload);
    }

    public function sendVenue(string $chatId, float $latitude, float $longitude, string $title, string $address, array $options = []): array
    {
        $payload = [
            'chat_id' => $chatId,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'title' => $title,
            'address' => $address,
        ];

        return $this->send('sendVenue', $payload);
    }

    public function pinMessage(string $chatId, int $messageId, bool $disableNotification = false): array
    {
        return $this->send('pinChatMessage', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'disable_notification' => $disableNotification,
        ]);
    }

    public function unpinMessage(string $chatId, ?int $messageId = null): array
    {
        $payload = ['chat_id' => $chatId];

        if ($messageId) {
            $payload['message_id'] = $messageId;
        }

        return $this->send('unpinChatMessage', $payload);
    }

    public function getFile(string $fileId): array
    {
        return $this->send('getFile', [
            'file_id' => $fileId,
        ]);
    }
    public function sendVoice(string $chatId, string $voice, ?string $caption = null, array $options = []): array
{
    $payload = [
        'chat_id' => $chatId,
        'voice' => $voice,
        'caption' => $caption,
        'parse_mode' => $options['parse_mode'] ?? 'HTML',
    ];

    return $this->send('sendVoice', $payload);
}

public function sendAudio(string $chatId, string $audio, ?string $caption = null, array $options = []): array
{
    $payload = [
        'chat_id' => $chatId,
        'audio' => $audio,
        'caption' => $caption,
        'parse_mode' => $options['parse_mode'] ?? 'HTML',
    ];

    return $this->send('sendAudio', $payload);
}

public function sendMediaGroup(string $chatId, array $mediaList): array
{
    $payload = [
        'chat_id' => $chatId,
        'media' => json_encode($mediaList),
    ];

    return $this->send('sendMediaGroup', $payload);
}

public function sendBulkMessage(array $chatIds, string $text, array $options = [], ?string $queue = null): void
{
    foreach ($chatIds as $chatId) {
        SendTelegramMessageJob::dispatch($this->botName, $chatId, $text, $options)
            ->onQueue($queue ?? config('telegram-sender.default_queue', 'default'));
    }
}

public function sendPhotoFromContent(string $chatId, string $fileContent, string $filename = 'image.jpg', ?string $caption = null): array
{
    $stream = fopen('php://temp', 'r+');
    fwrite($stream, $fileContent);
    rewind($stream);

    $url = $this->getBaseUrl('sendPhoto');

    $response = $this->buildHttpClient()
        ->attach('photo', $stream, $filename)
        ->post($url, [
            'chat_id' => $chatId,
            'caption' => $caption,
            'parse_mode' => 'HTML',
        ]);

    if (!$response->successful()) {
        throw new TelegramException("Telegram API error (sendPhotoFromContent): " . $response->body());
    }

    return $response->json();
}

}