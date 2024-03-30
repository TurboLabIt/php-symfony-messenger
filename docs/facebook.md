# Post a message to a page

1. register on [developers.facebook.com](https://developers.facebook.com)
2. [create an app](https://developers.facebook.com/apps/creation/)
3. [open the Graph Explorer](https://developers.facebook.com/tools/explorer)
4. select your app from `Meta App`
5. grant the following permissions: `pages_manage_metadata`, `pages_manage_posts`, `pages_show_list`
6. click `Generate Access Token`
7. from the `User or Page` dropdown, select your new `Page Access Token`

Submit and you'll get the `id` and `name` of the Page:

````json
{
    "id": "101440452292345",
    "name": "TLI Test Channel"
}
````

1. copy [the variables](https://github.com/TurboLabIt/php-symfony-messenger/blob/main/.env) to your local `.env` file.
2. assign the token and page ID values to the variables

You can now send a message with

````php
$messenger->sendMessageToPage($messageText)`
````

👉 [Check the example here](https://github.com/TurboLabIt/php-symfony-messenger/blob/main/tests/FacebookTest.php)
