<?php
namespace TurboLabIt\Messengers\Tests;

use PHPUnit\Framework\Attributes\Depends;
use TurboLabIt\Messengers\TwitterMessenger;


class TwitterTest extends BaseT
{
    const TESTED_SERVICE_FQN    = 'TurboLabIt\Messengers\TwitterMessenger';
    const SAMPLE_TWEET_ID       = '1774480562780041652';

    public function testInstance() : TwitterMessenger { return $this->getInstance(); }


    #[Depends('testInstance')]
    public function testSendMessageToPage(TwitterMessenger $messenger)
    {
        $messageText = '📚 Come installare Symfony su Windows: la video-Guida Definitiva https://turbolab.it/2561';
        $newPostId = $messenger->sendMessage($messageText);

        $this->assertIsString($newPostId);
        $this->assertGreaterThanOrEqual( mb_strlen(static::SAMPLE_TWEET_ID), mb_strlen($newPostId));
    }
}
