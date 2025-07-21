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

### 📚 Full Method List

| Method                           | Description |
|----------------------------------|-------------|
| `sendMessage()`                  | Sends a text message with optional inline buttons |
| `sendPhoto()`                    | Sends a photo using a URL or File ID |
| `sendPhotoFile()`               | Sends a photo from local file path |
| `sendPhotoFromContent()`        | Sends a photo from raw file content (no disk file) |
| `sendDocumentFile()`            | Sends a document (e.g., PDF) from local file path |
| `sendVoice()`                   | Sends a voice message (OGG/voice note) |
| `sendAudio()`                   | Sends an audio file (e.g., MP3) |
| `sendPoll()`                    | Sends a poll with multiple choice options |
| `sendMediaGroup()`              | Sends a media group (album of photos/videos) |
| `sendReplyKeyboard()`           | Sends a reply keyboard (custom buttons) |
| `sendForceReply()`              | Sends a message with force reply option |
| `removeKeyboard()`              | Removes the current reply keyboard from chat |
| `sendChatAction()`              | Sends chat action like `typing`, `upload_photo`, etc. |
| `editMessageText()`             | Edits text of a previously sent message |
| `deleteMessage()`               | Deletes a sent message |
| `answerCallbackQuery()`         | Answers a button click (callback query) |
| `sendLocation()`                | Sends a geographic location (latitude/longitude) |
| `sendVenue()`                   | Sends a venue with title and address |
| `pinMessage()`                  | Pins a message in the chat |
| `unpinMessage()`                | Unpins a specific or the latest pinned message |
| `getFile()`                     | Retrieves file info by its `file_id` |

---

## 📃 License

MIT © AmirMohammad KatebSaber
---

## 🇮🇷 راهنمای کامل فارسی

### 🛠 نصب پکیج

```bash
composer require telehelper/telegram-sender
php artisan vendor:publish --tag=telegram-sender-config
```

### ⚙️ تنظیمات

در فایل `config/telegram-sender.php` تنظیمات مربوط به بات‌ها، پراکسی، لاگ و سایر موارد را انجام دهید. برای فعال‌سازی لاگ اختصاصی، در `config/logging.php` کانال `telegram` را اضافه کنید:

```php
'channels' => [
    'telegram' => [
        'driver' => 'single',
        'path' => storage_path('logs/telegram.log'),
        'level' => 'info',
    ],
]
```

### ✉️ ارسال پیام ساده

```php
Telegram::bot()->sendMessage($chatId, 'سلام دنیا');
```

### 📸 ارسال عکس

```php
Telegram::bot()->sendPhoto($chatId, 'https://example.com/image.jpg', 'توضیح تصویر');
```

### 📎 ارسال فایل (PDF و...)

```php
Telegram::bot()->sendDocument($chatId, '/path/to/file.pdf', 'توضیح فایل');
```

### 🔊 ارسال صدا و ویس

```php
Telegram::bot()->sendAudio($chatId, '/path/to/audio.mp3');
Telegram::bot()->sendVoice($chatId, '/path/to/voice.ogg');
```

### 📊 ارسال نظرسنجی (Poll)

```php
Telegram::bot()->sendPoll($chatId, 'بهترین گزینه کدام است؟', ['گزینه اول', 'گزینه دوم']);
```

### 🖼 ارسال آلبوم (Media Group)

```php
Telegram::bot()->sendMediaGroup($chatId, [
    ['type' => 'photo', 'media' => 'https://example.com/1.jpg'],
    ['type' => 'photo', 'media' => 'https://example.com/2.jpg'],
]);
```

### 🎛 ارسال پیام با دکمه‌های شیشه‌ای

```php
Telegram::bot()->sendMessage($chatId, 'انتخاب کنید:', [
    'reply_markup' => [
        'inline_keyboard' => [
            [['text' => 'گوگل', 'url' => 'https://google.com']],
            [['text' => 'ورود با تلگرام', 'login_url' => ['url' => 'https://your.site/login']]],
        ]
    ]
]);
```

### 📍 ارسال موقعیت مکانی

```php
Telegram::bot()->sendLocation($chatId, 35.6892, 51.3890); // تهران
```

---

## 📦 صف‌بندی پیام‌ها (Queue)

### ارسال پیام با صف دلخواه

```php
Telegram::bot()->queue('high')->sendMessage($chatId, 'پیام صف‌بندی شده!');
```

### ارسال گروهی پیام (Bulk)

```php
Telegram::bot()->sendBulkMessage([$id1, $id2, $id3], 'پیام به همه کاربران');
```

---

### 📚 لیست کامل متدها

| متد                             | توضیح |
|----------------------------------|-------|
| `sendMessage()`                  | ارسال پیام متنی با قابلیت دکمه‌های شیشه‌ای |
| `sendPhoto()`                    | ارسال عکس از طریق URL یا File ID |
| `sendPhotoFile()`               | ارسال عکس از طریق مسیر فایل لوکال |
| `sendPhotoFromContent()`        | ارسال عکس از محتوای فایل (بدون ذخیره در دیسک) |
| `sendDocumentFile()`            | ارسال فایل مستند مانند PDF |
| `sendVoice()`                   | ارسال پیام صوتی (voice) |
| `sendAudio()`                   | ارسال فایل صوتی (audio) مثل mp3 |
| `sendPoll()`                    | ارسال نظرسنجی (poll) |
| `sendMediaGroup()`              | ارسال گروه رسانه‌ای (آلبوم عکس یا ویدیو) |
| `sendReplyKeyboard()`           | ارسال پیام با کیبورد معمولی (Reply Keyboard) |
| `sendForceReply()`              | ارسال پیام با کیبورد Force Reply |
| `removeKeyboard()`              | حذف کیبورد فعلی از چت کاربر |
| `sendChatAction()`              | ارسال وضعیت تایپ/ضبط صدا مثل `typing`, `upload_photo` و ... |
| `editMessageText()`             | ویرایش متن پیام قبلی ارسال‌شده |
| `deleteMessage()`               | حذف پیام ارسال‌شده |
| `answerCallbackQuery()`         | پاسخ به کلیک روی دکمه‌های شیشه‌ای |
| `sendLocation()`                | ارسال موقعیت جغرافیایی |
| `sendVenue()`                   | ارسال مکان با عنوان و آدرس |
| `pinMessage()`                  | پین کردن یک پیام در چت |
| `unpinMessage()`                | برداشتن پین از پیام خاص یا آخرین پیام پین‌شده |
| `getFile()`                     | دریافت اطلاعات فایل با استفاده از file_id |

---

## 📜 لایسنس

این پکیج تحت لایسنس MIT ارائه می‌شود.  
© امیرمحمد کاتب صابر

