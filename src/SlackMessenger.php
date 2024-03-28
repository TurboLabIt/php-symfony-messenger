<?php
namespace TurboLabIt\Messengers;

use stdClass;
use Symfony\Component\HttpFoundation\Request;


class SlackMessenger extends BaseMessenger
{
    public function sendMessageToChannel(string $message) : stdClass
    {
        return $this->sendMessage($message, [
            "channel" => $this->arrConfig["Slack"]["channelId"],
        ]);
    }


    public function sendErrorMessage(string $message) : stdClass
    {
        return $this->sendMessage($message, [
            "channel" => $this->arrConfig["Slack"]["errorsChannelId"],
        ]);
    }


    public function sendMessage(string $message, array $arrParams) : stdClass
    {
        return $this->apiCall('https://slack.com/api/chat.postMessage', array_merge($arrParams, [
            "text" => trim($message)
        ]));
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
