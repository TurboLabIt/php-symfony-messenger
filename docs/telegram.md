📚 https://core.telegram.org/bots/api#making-requests

## 🤖 1. Manage the Bot

1. [Interact with BotFather](https://telegram.me/BotFather) to create a new bot
2. From the list of your bots, get the token of the new bot
3. Check the profile of the bot and tap `Add to Group or Channel`
4. Add the bot to the channel you want to send messages to


## 📺 2. Get the handle of the channel you want to send messages to

1. Open the profile of the channel you want to send messages to
2. Check the `https://t.me/xxx` link: the handle (channel ID) you want is the `xxx` part


## 🌳 3. Setup the .env

1. copy [the variables](https://github.com/TurboLabIt/php-symfony-messenger/blob/main/.env) to your local `.env` file.
2. assign the token and channel ID values to the variables


## 📨 6. Send messages

You can now send a message with

````php
$messenger->sendMessageToChannel($messageText)`
````

👉 [Check the example here](https://github.com/TurboLabIt/php-symfony-messenger/blob/main/tests/TelegramTest.php)
