# Telegram Bot & Group Setup

This guide walks you through creating the Telegram bot and group used by the application to send reservation notifications to admins.

---

## 1. Create a Telegram Bot

1. Open Telegram and search for **@BotFather**.
2. Start a chat and send `/newbot`.
3. Follow the prompts to name your bot (e.g., `Alfia Reservations`) and choose a username (must end in `bot`, e.g., `alfia_reservations_bot`).
4. **Copy the Bot API Token** — it looks like `123456789:ABCdefGHIjklMNOpqrsTUVwxyz`.
5. Paste it into your `.env` file:
   ```env
   TELEGRAM_BOT_TOKEN=123456789:ABCdefGHIjklMNOpqrsTUVwxyz
   ```

---

## 2. Create a Telegram Group

1. In Telegram, tap **New Group**.
2. Name it (e.g., `Alfia Admin Alerts`) and optionally add a few admins.
3. Create the group.

---

## 3. Add the Bot to the Group

1. Open the group and tap the group name at the top.
2. Go to **Members** → **Add Members**.
3. Search for your bot by its username (e.g., `@alfia_reservations_bot`) and add it.
4. That's it — bots only need to be a **regular member** to post messages in groups.

---

## 4. Get the Group Chat ID

1. Send **any message** in the group (e.g., "test").
2. Open this URL in your browser (replace `<TOKEN>` with your actual bot token):
   ```
   https://api.telegram.org/bot<TOKEN>/getUpdates
   ```
3. Look for the `chat` object in the JSON response. It will look something like:
   ```json
   {
     "ok": true,
     "result": [
       {
         "message": {
           "chat": {
             "id": -123456789,
             "title": "Alfia Admin Alerts",
             "type": "group"
           }
         }
       }
     ]
   }
   ```
4. **Copy the `id` value** (e.g., `-123456789`).
5. Paste it into your `.env` file:
   ```env
   TELEGRAM_ADMIN_GROUP_ID=-123456789
   ```

> **Note:** If `getUpdates` returns an empty result (`[]`), make sure the bot has been added to the group and you have sent at least one message. If a webhook is already configured for the bot, `getUpdates` will not work.

---

## 5. Share the Group with Admins

1. Open the group info and copy the **Invite Link**.
2. Share the link with all admins — they can join the group and start receiving reservation notifications immediately.

---

## 6. Test the Integration

1. Ensure your `.env` values are saved:
   ```env
   TELEGRAM_BOT_TOKEN=your-bot-token
   TELEGRAM_ADMIN_GROUP_ID=-123your-group-id
   ```
2. Create a reservation through the public form or the admin panel.
3. You should see a notification appear in the Telegram group within seconds (notifications are queued, so ensure your queue worker is running if using `QUEUE_CONNECTION=database`).

---

## Troubleshooting

- **Empty `getUpdates` response:** Make sure the bot is a member of the group and that no webhook is set on the bot.
- **Messages not appearing:** Check that your queue worker is running (`php artisan queue:work` or `php artisan queue:listen`).
- **Bot can't post:** Re-check that the bot is actually in the group (you may have accidentally removed it).
