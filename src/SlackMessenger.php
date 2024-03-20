<?php
namespace TurboLabIt\Messengers;

use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;


class SlackMessenger extends BaseMessenger
{
    public function sendMessageToChannel(string $message) : stdClass
    {
        $message = trim($message);

        if( empty($message) ) {
            throw new InvalidParameterException("SlackMessenger: message to send cannot be empty");
        }

        $arrParams = [
            "channel"   => $this->arrConfig["Slack"]["channelId"],
            "text"      => $message
        ];

        $oJson = $this->apiCall('https://slack.com/api/chat.postMessage', $arrParams);
        return $oJson;
    }


    protected function apiCall(string $endPoint, array $arrParams = [], string $method = Request::METHOD_POST, array $arrHeaders = []) : stdClass
    {
        $disabled = array_key_exists("enabled", $this->arrConfig["Slack"]) && !$this->arrConfig["Slack"]["enabled"];
        if($disabled)  {
            return (object)["enabled" => false];
        }

        $arrHeaders = array_merge($arrHeaders, ["Authorization" => "Bearer " . $this->arrConfig["Slack"]["token"]]);

        $this->lastResponse = $this->httpClient->request($method, $endPoint, [
            "headers"   => $arrHeaders,
            "body"      => $arrParams
        ]);

        $content = $this->lastResponse->getContent(false);
        $oJson = json_decode($content);
        return $oJson;
    }
}
