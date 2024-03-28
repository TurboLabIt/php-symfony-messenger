<?php
namespace TurboLabIt\Messengers;

use stdClass;
use Symfony\Component\HttpFoundation\Request;


/**
 * 📚 https://core.telegram.org/bots/api#making-requests
 */
class TelegramMessenger extends BaseMessenger
{
    const ENDPOINT = 'https://api.telegram.org/bot';
    protected array $arrMessageButtons = [];


    public function setMessageButtons(array $arrMessageButtons) : static
    {
        $this->arrMessageButtons = $arrMessageButtons;
        return $this;
    }


    protected function getEndPoint() : string
    {
        return static::ENDPOINT . $this->arrConfig["Telegram"]["token"] . "/";
    }


    public function sendMessageToChannel(string $message, array $arrParams = []) : stdClass
    {
        return $this->sendMessage($message, array_merge([
            "chat_id" => $this->arrConfig["Telegram"]["channelId"],
        ], $arrParams));
    }


    public function sendErrorMessage(string $message, array $arrParams = []) : stdClass
    {
        return $this->sendMessage($message, array_merge([
            "chat_id" => $this->arrConfig["Telegram"]["errorsChannelId"],
        ], $arrParams));
    }


    public function sendMessage(string $message, array $arrParams) : stdClass
    {
        // 📚 https://core.telegram.org/bots/api#sendmessage

        $arrParams = array_merge_recursive([
            "text"                      => $message,
            "parse_mode"                => "HTML",
            "disable_web_page_preview"  => 0,
            "disable_notification"      => 0
        ], $arrParams);

        if( !empty($this->arrMessageButtons) ) {
            $arrParams["reply_markup"] = json_encode([
                "inline_keyboard" => [$this->arrMessageButtons]
            ]);
        }

        $endPoint = $this->getEndPoint() . 'sendMessage';
        return $this->apiCall($endPoint, $arrParams);
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
