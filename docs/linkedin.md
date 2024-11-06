📚 [Posts API](https://learn.microsoft.com/en-us/linkedin/marketing/community-management/shares/posts-api) -


## 🏢 1. Create a company Page

[linkedin.com/company/setup/new](https://www.linkedin.com/company/setup/new)


## 🤖 2. Create a LinkedIn App

Create a new app from [linkedin.com/developers/apps/new](https://www.linkedin.com/developers/apps/new).

Add the *LinkedIn Page URL* of the page you want to post to.

Don't add any *Product* yet.


## 🔄 3. Verify the association between the app and the page

Go to the `Settings` verify the association between the app and the page.


## 🛍️ 4. Products

To post on a page, you need the `w_organization_social` permission. Go to the `Products` tab and add one of these:

- `Advertising API` <-- use this!
- `Community Management API` - this is harder to get, because it must be the only one selected

Proceed with the request providing the required company information, then submit the *Access submit request*.


## ☎️ 5. Callback URLs

Go to the `Auth` tab and add your callback URLs (dev, staging, prod) like this:

`https://example.com/setup/linkedin/auth/code-return-to/`


## 🔑 6. API credentials

From the `Auth` tab, get your API credentials (`Client ID` and `Primary Client Secret`).

Set the [corresponding parameters](https://github.com/TurboLabIt/php-symfony-messenger/blob/main/.env) in your own `.env`.


## 🐑 7. Copy the routes yaml file

Copy `config/routes/turbolabit_messengers.yaml` to your own Symfony project.


## 👮 8. Authorize the app

Open `https://example.com/setup/linkedin/auth/` and follow the authorization flow.

You MUST use the LinkedIn account of an administrator of the company page connected to your app.


## 🚀 9. Post!

You can now share a URL on the page with

````php
$messenger->sendUrl('https://turbolab.it')`
````

👉 [Check the example here](https://github.com/TurboLabIt/TurboLab.it/blob/main/src/Command/ShareOnSocialCommand.php)
