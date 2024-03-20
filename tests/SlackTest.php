<?php
namespace TurboLabIt\Messengers\Tests;

use PHPUnit\Framework\Attributes\Depends;
use TurboLabIt\Messengers\SlackMessenger;


class SlackTest extends BaseT
{
    const TESTED_SERVICE_FQN = 'TurboLabIt\Messengers\SlackMessenger';

    public function testInstance() : SlackMessenger { return $this->getInstance(); }


    #[Depends('testInstance')]
    public function testSendMessageToChannel(SlackMessenger $messenger)
    {
        $messageText = "*🧪 this is a test from https://github.com/TurboLabIt/php-symfony-messenger *";
        $oJson = $messenger->sendMessageToChannel($messageText);

        $this->assertInstanceOf('\stdClass', $oJson);
        $this->assertEquals(true, $oJson->ok);
    }
}
