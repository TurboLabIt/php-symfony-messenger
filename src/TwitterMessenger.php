<?php
namespace TurboLabIt\MessengersBundle;

use Abraham\TwitterOAuth\TwitterOAuth;


/**
 * 📚 https://twitteroauth.com/
 */
class TwitterMessenger extends BaseMessenger
{
    const SERVICE_NAME = self::SERVICE_TWITTER;

    protected TwitterOAuth $twitterOAuth;


    public function __construct(protected array $arrConfig)
    {
        $oConfig = (object)$arrConfig["Twitter"];
        $this->twitterOAuth =
            new TwitterOAuth($oConfig->apiKey, $oConfig->apiSecret, $oConfig->accessToken, $oConfig->accessTokenSecret);
    }


    public function sendMessage(?string $message) : string
    {
        $oJson =
            $this->twitterOAuth->post("tweets", [
                "text" => $this->messageEncoder($message)
            ]);

        $httpStatus = $this->twitterOAuth->getLastHttpCode();

        if( $httpStatus < 200 || $httpStatus > 299 ) {
            throw new TwitterException($oJson->title . ": " . $oJson->detail);
        }

        $tweetId = $oJson->data->id ?? null;
        if( empty($tweetId) ) {
            throw new TwitterException("No valid postId returned by the Twitter endpoint");
        }

        return $tweetId;
    }


    public function getPageUrl() : string
    {
        $handle = trim($this->arrConfig["Twitter"]["id"], '@');
        return 'https://twitter.com/' . $handle;
    }


    public function buildMessageUrl(string|int $postId) : string
        { return $this->getPageUrl() . "/status/" . $postId; }
}
