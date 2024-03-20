<?php
namespace TurboLabIt\Messengers;

use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;


/**
 * 📚 https://core.telegram.org/bots/api#making-requests
 */
class TelegramMessenger extends BaseMessenger
{
    const ENDPOINT = 'https://api.telegram.org/bot';


    protected function getEndPoint() : string
    {
        return static::ENDPOINT . $this->arrConfig["Telegram"]["token"] . "/";
    }


    public function sendMessageToChannel(string $message, array $arrButtons = []) : stdClass
    {
        $message = trim($message);

        if( empty($message) ) {
            throw new InvalidParameterException("TelegramMessenger: message to send cannot be empty");
        }

        $arrParams = [
            "chat_id"                   => $this->arrConfig["Telegram"]["channelId"],
            "text"                      => $message,
            "parse_mode"                => "HTML",
            "disable_web_page_preview"  => 0,
            "disable_notification"      => 0,
        ];

        if( !empty($arrButtons) ) {
            $arrParams["reply_markup"] = json_encode([
                "inline_keyboard" => [$arrButtons]
            ]);
        }

        // 📚 https://core.telegram.org/bots/api#sendmessage
        $endPoint = $this->getEndPoint() . 'sendMessage';
        $oJson = $this->apiCall($endPoint, $arrParams);
        return $oJson;
    }


    protected function apiCall(string $endPoint, array $arrParam, string $method = Request::METHOD_POST, array $arrHeaders = []) : stdClass
    {
        $this->lastResponse = $this->httpClient->request($method, $endPoint, [
            "headers"   => $arrHeaders,
            "query"     => $arrParam
        ]);

        $content = $this->lastResponse->getContent(false);
        $oJson = json_decode($content);
        return $oJson;
    }
}
