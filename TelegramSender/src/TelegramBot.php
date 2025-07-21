<?php

namespace TeleHelper\TelegramSender;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

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
                throw new TelegramException("Telegram API error ({$method}): " . $response->body());
            }

            return $response->json();
        } catch (\Throwable $e) {
            throw new TelegramException("Send failed ({$method}): " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function sendMessage(
        string $text,
        array $options = []
    ): array {
        $payload = [
            'chat_id' => $this->chatId,
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

    public function sendPhoto(
        string $photo,
        ?string $caption = null,
        array $options = []
    ): array {
        $payload = [
            'chat_id' => $this->chatId,
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

    public function sendDocument(
        string $fileUrl,
        ?string $caption = null,
        array $options = []
    ): array {
        $payload = [
            'chat_id' => $this->chatId,
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

    public function sendMediaGroup(array $media): array
    {
        // ساختار:
        // [
        //   ['type' => 'photo', 'media' => 'url', 'caption' => '...', 'parse_mode' => 'HTML'],
        //   ['type' => 'document', 'media' => 'url']
        // ]
        $payload = [
            'chat_id' => $this->chatId,
            'media' => json_encode($media),
        ];

        return $this->send('sendMediaGroup', $payload);
    }

    public function editMessageText(
        int $messageId,
        string $newText,
        array $options = []
    ): array {
        $payload = [
            'chat_id' => $this->chatId,
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

    public function deleteMessage(int $messageId): array
    {
        return $this->send('deleteMessage', [
            'chat_id' => $this->chatId,
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
        public function sendReplyKeyboard(
        string $text,
        array $keyboard,
        array $options = []
    ): array {
        $payload = [
            'chat_id' => $this->chatId,
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

    public function sendForceReply(
        string $text,
        array $options = []
    ): array {
        $payload = [
            'chat_id' => $this->chatId,
            'text' => $text,
            'parse_mode' => $options['parse_mode'] ?? 'HTML',
            'reply_markup' => json_encode([
                'force_reply' => true,
                'selective' => $options['selective'] ?? false,
            ]),
        ];

        return $this->send('sendMessage', $payload);
    }

    public function removeKeyboard(
        string $text,
        array $options = []
    ): array {
        $payload = [
            'chat_id' => $this->chatId,
            'text' => $text,
            'parse_mode' => $options['parse_mode'] ?? 'HTML',
            'reply_markup' => json_encode([
                'remove_keyboard' => true,
            ]),
        ];

        return $this->send('sendMessage', $payload);
    }

    public function sendLocation(
        float $latitude,
        float $longitude,
        array $options = []
    ): array {
        $payload = [
            'chat_id' => $this->chatId,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'disable_notification' => $options['disable_notification'] ?? false,
            'horizontal_accuracy' => $options['accuracy'] ?? null,
        ];

        return $this->send('sendLocation', $payload);
    }

    public function sendChatAction(string $action): array
    {
        // action must be one of: typing, upload_photo, record_video, upload_video, record_voice, upload_voice, upload_document, find_location, etc.
        return $this->send('sendChatAction', [
            'chat_id' => $this->chatId,
            'action' => $action,
        ]);
    }
}