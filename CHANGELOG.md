# 📦 Changelog

## v1.0.0 – Initial release of TelegramSender

🎉 First release of `telehelper/telegram-sender`

This version includes a powerful and flexible Telegram message sender for Laravel:

### ✉️ Message Types
- Send text messages with MarkdownV2 or HTML parse modes
- Send photos (by file ID, URL, or file upload)
- Send documents (PDF, DOCX, etc.)
- Send voice (`sendVoice`) and audio (`sendAudio`) messages
- Send polls (regular or quiz type)
- Send location and venues
- Send media group (album of photos/videos)
- Send chat actions (typing, uploading, etc.)

### 🔘 Keyboards & Buttons
- Inline keyboard support
  - Including `url`, `login_url`, `callback_data`, `switch_inline_query` buttons
- Reply keyboard support (with auto-resize & one-time options)
- Remove keyboard
- Force reply support

### 🔁 Message Control
- Edit message text with inline keyboard
- Delete messages
- Pin and unpin messages

### 🔐 Infrastructure & Features
- Multi-bot support (manage multiple Telegram bots)
- Proxy support (with auth)
- Full queue support:
  - Dispatch via Laravel queue
  - Bulk queue (`sendBulkMessage`)
  - Custom queue selection per message
- Send media from file content (`php://memory`)
- Logging support:
  - Enable/disable logs via config
  - Custom log channel
- Clean exception handling with dedicated `TelegramException`
- Configurable via published config file
- Includes `artisan telegram:test` command for local testing

---
