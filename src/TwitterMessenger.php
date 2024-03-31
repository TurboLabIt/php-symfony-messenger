<?php
namespace TurboLabIt\Messengers;

use Abraham\TwitterOAuth\TwitterOAuth;


/**
 * 📚 https://twitteroauth.com/
 */
class TwitterMessenger extends BaseMessenger
{
    protected TwitterOAuth $twitterOAuth;


    public function __construct(array $arrConfig)
    {
        $oConfig = (object)$arrConfig["Twitter"];

        $this->twitterOAuth =
            new TwitterOAuth($oConfig->apiKey, $oConfig->apiSecret, $oConfig->accessToken, $oConfig->accessTokenSecret);
    }


    public function sendMessage(?string $message) : string
    {
        $oJson      = $this->twitterOAuth->post("tweets", ["text" => $message]);
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
}
