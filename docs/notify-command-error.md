## config/services.yaml

````yaml
services:

    App\EventListener\CommandFailureListener:
        tags:
            - { name: kernel.event_listener, method: onCommandFailure,  event: console.error }
````

## src/EventListener/CommandFailureListener.php

````php
<?php
namespace App\EventListener;

use Symfony\Component\Console\Event\ConsoleErrorEvent;
use TurboLabIt\Messengers\SlackMessenger;


class CommandFailureListener
{
    public function __construct(protected SlackMessenger $messenger)
    { }


    public function onCommandFailure(ConsoleErrorEvent $event)
    {
        // https://symfony.com/doc/current/components/console/events.html#the-consoleevents-error-event

        $text =
            "🛑 my-app is failing" . PHP_EOL . PHP_EOL .
            "Command: *" . $event->getCommand()?->getName() . "*" . PHP_EOL .
            "Error: *" . $event->getError()->getMessage() . "*";

        $this->messenger->sendErrorMessage($text);
    }
}

````
