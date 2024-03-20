## 🤖 1. Create a Slack App

1. [api.slack.com](https://api.slack.com/apps)
2. `Create New App` -> `From scratch`
3. Fill `App Name` and pick a workspace

Select the new app from the dropdown on the left


## 👮 2. App permissions

1. Click `OAuth & Permissions` on the left
2. Under `Bot Token Scopes`, click `Add an OAuth Scope`

Grant the following permissions:

- `chat:write`
- `chat:write.public`
- `chat:write:customize`


## 🚦 3. Install the Slack App to the Workspace

Click `Install App` on the left and follow the on-screen instructions.

The `OAuth Tokens for Your Workspace` is then shown on the page. It looks like this:

> xoxb-1234567890-...-...

You can also get the token from `OAuth & Permissions` on the left (section `OAuth Tokens for Your Workspace`, `Bot User OAuth Token`).


## 📺 4. Get the ID of the channel to post to

1. Open on the channel you want to post to in Slack
2. Click on the channel name (top section)
3. Copy the Channel ID (shown at the bottom of the modal window)


## 🌳 5. Setup the .env

1. copy [the variables](https://github.com/TurboLabIt/php-symfony-messenger/blob/main/.env) to your local `.env` file.
2. assign the token and channel ID values to the variables


## 📨 6. Send messages

You can now send a message with

````php
$messenger->sendMessageToChannel($messageText)`
````

👉 [Check the example here](https://github.com/TurboLabIt/php-symfony-messenger/blob/main/tests/SlackTest.php)
