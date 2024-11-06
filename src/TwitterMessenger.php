<?php
namespace TurboLabIt\MessengersBundle;

use Abraham\TwitterOAuth\TwitterOAuth;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


/**
 * 📚 https://github.com/TurboLabIt/php-symfony-messenger/blob/main/docs/twitter.md
 */
class TwitterMessenger extends BaseMessenger
{
    const SERVICE_NAME = self::SERVICE_TWITTER;

    protected TwitterOAuth $twitterOAuth;


    public function __construct(protected array $arrConfig, protected ParameterBagInterface $parameters)
    {
        $oConfig = (object)$arrConfig["Twitter"];
        $this->twitterOAuth = new TwitterOAuth(
            $oConfig->apiKey, $oConfig->apiSecret, $oConfig->accessToken, $oConfig->accessTokenSecret
        );
    }


    public function sendMessage(?string $message) : string
    {
        $tag = $this->getEnvTag();
        if( !empty($tag) ) {
            $message = "$tag $message";
        }

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
