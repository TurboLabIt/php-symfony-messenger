<?php
namespace TurboLabIt\Messengers\Tests;

use PHPUnit\Framework\Attributes\Depends;
use TurboLabIt\Messengers\FacebookMessenger;


class FacebookTest extends BaseT
{
    const TESTED_SERVICE_FQN = 'TurboLabIt\Messengers\FacebookMessenger';

    public function testInstance() : FacebookMessenger { return $this->getInstance(); }


    #[Depends('testInstance')]
    public function testSendMessageToPage(FacebookMessenger $messenger)
    {
        $messageText = '📚 Come installare Symfony su Windows: la video-Guida Definitiva';
        //$messageHtml = '<b><a href="https://turbolab.it/2561">' . $messageText . '</a></b>';

        $newPostId = $messenger->sendMessageToPage($messageText);

        $this->assertIsString($newPostId);
        $this->assertGreaterThanOrEqual( mb_strlen('101440452292345_439921855229876'), mb_strlen($newPostId));
    }
}
