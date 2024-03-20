<?php
namespace TurboLabIt\Messengers\Tests;

use PHPUnit\Framework\Attributes\Depends;
use TurboLabIt\Messengers\TelegramMessenger;


class TelegramTest extends BaseT
{
    const TESTED_SERVICE_FQN = 'TurboLabIt\Messengers\TelegramMessenger';

    public function testInstance() : TelegramMessenger { return $this->getInstance(); }


    #[Depends('testInstance')]
    public function testSendMessageToChannel(TelegramMessenger $messenger)
    {
        $messageText = '📚 Come installare Symfony su Windows: la video-Guida Definitiva';

        $result =
            $messenger->sendMessageToChannel(
                '<b><a href="https://turbolab.it/2561">' . $messageText . '</a></b>', [
                    [
                    "text"  => "👉🏻 LEGGI L'ARTICOLO 👈🏻",
                    "url"   => "https://turbolab.it/2561"
                    ]
                ]
            );

        $this->assertInstanceOf('\stdClass', $result);
        $this->assertEquals(true, $result->ok);
        $this->assertEquals($messageText, $result->result->text);
    }
}
