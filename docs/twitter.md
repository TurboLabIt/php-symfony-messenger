1. register on [developer.twitter.com](https://developer.twitter.com)
2. on your [developer dashboard](https://developer.twitter.com/en/portal/dashboard) you should see a `Default project` with a `Project app`

Customize the app. From `Settings`, run `User authentication settings`

![screenshot](https://raw.githubusercontent.com/TurboLabIt/php-symfony-messenger/main/docs/images/01_twitter-app-settings.png)

Set:

- `App permissions` ➡ `Read and write`
- `Type of App` ➡ `Web app (Confidential client)`
- `Callback URI` ➡ `https://example.com` (literally)

![screenshot](https://raw.githubusercontent.com/TurboLabIt/php-symfony-messenger/main/docs/images/02_twitter-oauth-settings.png)

Save, go back to the app and switch to `Keys and tokens`.

Check the `Access Token and Secret`: it must read

> Created with Read and Write permissions

![screenshot](https://raw.githubusercontent.com/TurboLabIt/php-symfony-messenger/main/docs/images/03_twitter-read-write-permission.png)

Now copy [the variables](https://github.com/TurboLabIt/php-symfony-messenger/blob/main/.env) to your local `.env` file and set the values from the Twitter app.

You can now post a message on the page with

````php
$messenger->sendMessage($messageText)`
````

👉 [Check the example here](https://github.com/TurboLabIt/php-symfony-messenger/blob/main/tests/TwitterTest.php)
