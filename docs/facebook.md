# Post a message to a page

📚 [Facebook Pages API](https://developers.facebook.com/docs/pages-api)

1. register on [developers.facebook.com](https://developers.facebook.com)
2. [create an app](https://developers.facebook.com/apps/creation/): choose `Other` -> `Business`, don't link any "Business portfolio"
3. 🏁 as soon as you reach `Add products to your app`, the app is ready
3. [open the Graph Explorer](https://developers.facebook.com/tools/explorer)
4. from the `Meta App` dropdown, select your just-created-app
5. grant the following permissions: `pages_manage_metadata`, `pages_manage_posts`, `pages_show_list`
5. from the `User or Page` dropdown, select `Get Page Access Token` | this will start a wizard to grant access to the page you want to post to
7. from the `User or Page` dropdown, select your Page
8. click `Generate Access Token`

Submit and you'll get the `id` and `name` of the Page:

````json
{
    "id": "10...45",
    "name": "TLI Test Channel"
}
````

This is a SHORT-LIVED (~2 hours) token. Input it into the [Access Token Debugger](https://developers.facebook.com/tools/debug/accesstoken/) and click `Extend Access Token`. This generates a LONG-LIVED (~2 months) token.

Now go back to the Graph Explorer. From the `User or Page` dropdown, select `User Token`. Submit and you'll get your own `user-id`:

````json
{
  "id": "10...99",
  "name": "Gianluigi NeverEnough Zanettini"
}
````

Customize this cURL request and fire it away:

````shell
curl -i -X GET "https://graph.facebook.com/v19.0/{user-id}/accounts?access_token={long-lived-access-token}"
````

This will return a JSON with a `access_token` fields. That token should be never-expiring: test it with the [Access Token Debugger](https://developers.facebook.com/tools/debug/accesstoken/) and you should get:

> Expires: Never
>
> Data Access Expires: 1719608325 (in about 3 months)

(don't mind the `Data Access Expires`: it's not need just to post)

1. copy [the variables](https://github.com/TurboLabIt/php-symfony-messenger/blob/main/.env) to your local `.env` file.
2. assign the never-expiring token (the last one) and page ID values to the variables

You can now post a message on the page with

````php
$messenger->sendMessageToPage($messageText)`
````

Or share a URL:

````php
$messenger->sendUrlToPage('https://turbolab.it')`
````

👉 [Check the example here](https://github.com/TurboLabIt/php-symfony-messenger/blob/main/tests/FacebookTest.php)
