<?php
namespace TurboLabIt\Messengers;

use stdClass;
use Symfony\Component\HttpFoundation\Request;


/**
 * 📚 https://developers.facebook.com/docs/pages-api/posts/
 */
class FacebookMessenger extends BaseMessenger
{
    const ENDPOINT = 'https://graph.facebook.com/v19.0/';


    public function sendMessageToPage(?string $message, array $arrParams = []) : string
    {
        $endpoint   = static::ENDPOINT . $this->arrConfig["Facebook"]["page"]["id"] . "/feed";
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


    public function sendUrlToPage(string $url, array $arrParams = []) : string
    {
        return $this->sendMessageToPage(null, array_merge([
            "link" => $url
        ], $arrParams));
    }


    protected function apiCall(string $endPoint, array $arrParam, string $method = Request::METHOD_POST, array $arrHeaders = []) : stdClass
    {
        $this->lastResponse = $this->httpClient->request($method, $endPoint, [
            "headers"   => array_merge([
                "Content-Type"  => "application/json"
            ], $arrHeaders),
            "query"     => $arrParam
        ]);

        $content = $this->lastResponse->getContent(false);

        if( empty($content) ) {
            throw new FacebookException("Empty response from the Facebook endpoint");
        }

        $oJson = json_decode($content);

        if( empty($oJson) ) {
            throw new FacebookException("JSON response decoding error");
        }

        return $oJson;
    }
}
