# 📦 telehelper/telegram-sender

A powerful Laravel package to send Telegram messages with full control, multi-bot support, media sending, queueing, logging, and keyboard buttons.

## 🚀 Features

- ✅ Multi-bot support
- ✅ Send text, photo, document, voice, audio, polls, media groups
- ✅ Inline buttons (URL, callback, login_url, switch_inline_query)
- ✅ Reply keyboards
- ✅ Proxy support
- ✅ Dedicated logging channel
- ✅ Full error handling
- ✅ Queue support (with optional custom queue name)
- ✅ Bulk message dispatching
- ✅ Send file content from memory
- ✅ Artisan test command
- ✅ Easy config publishing

---

## ⚙️ Installation

```bash
composer require telehelper/telegram-sender
php artisan vendor:publish --tag=telegram-sender-config
```

---

## 🧪 Artisan Test Command

```bash
php artisan telegram:send
    --bot=default
    --chat_id=12345678
    --text="Hello Telegram!"
```

---

## ⚙️ Configuration (`config/telegram-sender.php`)

```php
return [
    'default_bot' => 'default',

    'bots' => [
        [
            'name' => 'default',
            'token' => env('TELEGRAM_BOT_TOKEN'),
        ],
    ],

    'log_enabled' => true,
    'log_channel' => 'telegram',

    'proxy' => [
        'enabled' => false,
        'host' => null,
        'port' => null,
        'username' => null,
        'password' => null,
        'type' => 'http',
    ],
];
```

Log channel in `config/logging.php`:

```php
'channels' => [
    'telegram' => [
        'driver' => 'single',
        'path' => storage_path('logs/telegram.log'),
        'level' => 'info',
    ],
]
```

---

## 💬 Usage

### 🔹 Send Text Message

```php
use TeleHelper\TelegramSender\Facades\Telegram;

Telegram::bot('default')->sendMessage($chatId, 'Hello world!');
```

### 🔹 Send Photo

```php
Telegram::bot()->sendPhoto($chatId, 'https://example.com/image.jpg', 'Caption here');
```

### 🔹 Send Document

```php
Telegram::bot()->sendDocument($chatId, '/path/to/file.pdf', 'Optional caption');
```

### 🔹 Send Audio / Voice

```php
Telegram::bot()->sendAudio($chatId, '/path/to/audio.mp3');
Telegram::bot()->sendVoice($chatId, '/path/to/voice.ogg');
```

### 🔹 Send Poll

```php
Telegram::bot()->sendPoll($chatId, 'Your favorite?', ['Option 1', 'Option 2']);
```

### 🔹 Send Media Group (Album)

```php
Telegram::bot()->sendMediaGroup($chatId, [
    ['type' => 'photo', 'media' => 'https://example.com/image1.jpg'],
    ['type' => 'photo', 'media' => 'https://example.com/image2.jpg'],
]);
```

### 🔹 Send Message with Buttons

```php
Telegram::bot()->sendMessage($chatId, 'Choose:', [
    'reply_markup' => [
        'inline_keyboard' => [
            [['text' => 'Google', 'url' => 'https://google.com']],
            [['text' => 'Login', 'login_url' => ['url' => 'https://your.site/login']]],
        ]
    ]
]);
```

---

## 🧵 Using Queues

### Single queued message

```php
Telegram::bot()->queue('high')->sendMessage($chatId, 'Queued message!');
```

### Bulk message dispatching

```php
Telegram::bot()->sendBulkMessage([$id1, $id2, $id3], 'Bulk message');
```

---

## 📋 List of Available Methods

| Method              | Description                       |
|---------------------|-----------------------------------|
| sendMessage         | Send text message                 |
| sendPhoto           | Send image                        |
| sendDocument        | Send document (pdf, zip, etc)     |
| sendAudio           | Send audio                        |
| sendVoice           | Send voice note                   |
| sendPoll            | Send Telegram poll                |
| sendMediaGroup      | Send album of media               |
| deleteMessage       | Delete a message                  |
| sendLocation        | Send location (lat/lng)           |
| sendChatAction      | Typing indicator                  |
| sendBulkMessage     | Send message to multiple users    |
| withQueue / queue   | Set custom queue name             |

---

## 🇮🇷 راهنمای فارسی

### 🛠 نصب

```bash
composer require telehelper/telegram-sender
php artisan vendor:publish --tag=telegram-sender-config
```

### ✉️ ارسال پیام ساده

```php
Telegram::bot()->sendMessage($chatId, 'سلام دنیا');
```

### 🎛 امکانات

- ارسال پیام با دکمه‌های شیشه‌ای، آدرس، لاگین، سوییچ اینلاین
- ارسال انواع رسانه‌ها (عکس، پی‌دی‌اف، صوت، آلبوم و ...)
- پشتیبانی از چند بات
- صف‌بندی پیام‌ها
- ارسال به چند کاربر هم‌زمان
- لاگ اختصاصی
- تنظیم پراکسی

---

## 📃 License

MIT © AmirMohammad KatebSaber