<?php
namespace TurboLabIt\Messengers\Tests;

use PHPUnit\Framework\Attributes\Depends;
use TurboLabIt\Messengers\FacebookMessenger;


class FacebookTest extends BaseT
{
    const TESTED_SERVICE_FQN    = 'TurboLabIt\Messengers\FacebookMessenger';
    const SAMPLE_POST_ID        = '101440452292345_439921855229876';

    public function testInstance() : FacebookMessenger { return $this->getInstance(); }


    #[Depends('testInstance')]
    public function testPageUrl(FacebookMessenger $messenger)
    {
        $pageUrl = $messenger->getPageUrl();
        $this->assertNotEquals(FacebookMessenger::WEBURL, $pageUrl);
    }


    #[Depends('testInstance')]
    public function testSendMessageToPage(FacebookMessenger $messenger)
    {
        $messageText = '📚 Come installare Symfony su Windows: la video-Guida Definitiva';
        //$messageHtml = '<b><a href="https://turbolab.it/2561">' . $messageText . '</a></b>';

        $newPostId = $messenger->sendMessageToPage($messageText);

        $this->assertIsString($newPostId);
        $this->assertGreaterThanOrEqual( mb_strlen(static::SAMPLE_POST_ID), mb_strlen($newPostId));

        return $newPostId;
    }


    #[Depends('testInstance')]
    public function testSendUrlToPage(FacebookMessenger $messenger)
    {
        $newPostId = $messenger->sendUrlToPage('https://turbolab.it/2561');

        $this->assertIsString($newPostId);
        $this->assertGreaterThanOrEqual( mb_strlen(static::SAMPLE_POST_ID), mb_strlen($newPostId));
    }


    #[Depends('testSendMessageToPage')]
    public function testNewMessageUrl(string|int $newPostId)
    {
        $newMessageUrl = $this->getInstance()->buildMessageUrl($newPostId);
        $this->assertEquals("https://www.facebook.com/$newPostId", $newMessageUrl);
    }
}
