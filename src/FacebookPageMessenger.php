<?php
namespace TurboLabIt\Messengers;

use stdClass;
use Symfony\Component\HttpFoundation\Request;


/**
 * 📚 https://developers.facebook.com/docs/pages-api/getting-started
 */
class FacebookPageMessenger extends BaseMessenger
{
    const ENDPOINT  = 'https://graph.facebook.com/v20.0/##PAGE-ID##/feed';
    const WEBURL    = 'https://www.facebook.com/##PAGE-ID##/posts/';


    public function sendMessage(?string $message, array $arrParams = []) : string
    {
        $endpoint   = str_replace('##PAGE-ID##', $this->arrConfig["Facebook"]["page"]["id"], static::ENDPOINT);
        $arrParams  = array_merge([
            "message"       => $message,
            "access_token"  => $this->arrConfig["Facebook"]["page"]["token"]
        ], $arrParams);

        $result = $this->apiCall($endpoint, $arrParams);
        if( empty($result->id) ) {
            throw new FacebookException("No valid postId returned by the Facebook endpoint");
        }

        // this is the ID of the new post
        return $result->id;
    }


    public function sendUrl(string $url, array $arrParams = []) : string
    {
        return
            $this->sendMessageToPage(null, array_merge([
                "link" => $url
            ], $arrParams));
    }


    protected function apiCall(string $endPoint, array $arrParam, string $method = Request::METHOD_POST, array $arrHeaders = []) : stdClass
    {
        $this->lastResponse = $this->httpClient->request($method, $endPoint, [
            "headers" => array_merge([
                "Content-Type" => "application/json"
            ], $arrHeaders),
            "query" => $arrParam
        ]);

        $content = $this->lastResponse->getContent(false);

        if( empty($content) ) {
            throw new FacebookException("Empty response from the Facebook endpoint");
        }

        $oJson = json_decode($content);

        if( empty($oJson) ) {
            throw new FacebookException("JSON response decoding error");
        }

        if( !empty($oJson->error) ) {
            throw new FacebookException($oJson->error->message);
        }

        return $oJson;
    }


    public function getPageUrl() : string
        { return str_ireplace('##PAGE-ID##', $this->arrConfig["Facebook"]["page"]["id"], static::WEBURL); }

    public function buildMessageUrl(string|int $postId) : string { return "https://www.facebook.com/$postId"; }
}
