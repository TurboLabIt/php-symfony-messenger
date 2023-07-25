# php-symfony-messenger
A collection of webclients to send messages to Slack, Facebook, Twitter, Telegram, ...


## 📦 1. Install it with composer

````bash
composer config repositories.turbolabit/php-symfony-messenger git https://github.com/TurboLabIt/php-symfony-messenger.git
composer require turbolabit/php-symfony-messenger:dev-main

````


## 🌳 2. .env

Get your Slack webhook endpoint from https://api.slack.com/apps

````
APP_SLACK_ENABLED=true
## Channel: #my-channel-name https://my-company.slack.com/archives/AAABBCCC
APP_SLACK_ENDPOINT=https://hooks.slack.com/services/AABBCC/AABBCC/AABBCC
## Channel: #my-channel-name https://my-company.slack.com/archives/AAABBCCC
APP_SLACK_ENDPOINT_ERRORS=https://hooks.slack.com/services/AABBCC/AABBCC/AABBCC

````


## ⚙️ 3. services.yaml

````yaml
    TurboLabIt\Messengers\SlackMessenger:
        arguments:
            $arrConfig:
                Slack:
                    enabled: '%env(bool:APP_SLACK_ENABLED)%'
                    endpoints:
                        main: '%env(APP_SLACK_ENDPOINT)%'
                        errors: '%env(APP_SLACK_ENDPOINT_ERRORS)%'
````

## 📨 4. Send messages

````php
<?php declare(strict_types=1);
namespace App\Command;

use TurboLabIt\Messengers\SlackMessenger;


class MyService
{
    public function __construct(protected SlackMessenger $slackMessenger)
    { }
    
    
    public function someFx()
    {
      // ...
      
      $this->slackMessenger->sendMessageToChannel($text);
      
      $this->slackMessenger->sendErrorMessage($text);
      
      // ...
    }
}

````


## 🛑 5. Get a notification on Command errors

[docs/notify-command-error.md](https://github.com/TurboLabIt/php-symfony-messenger/blob/main/docs/notify-command-error.md)


## 🧪 Test it

````bash
git clone git@github.com:TurboLabIt/php-doctrine-runtime-manager.git
cd php-doctrine-runtime-manager
clear && bash script/test_runner.sh

````
