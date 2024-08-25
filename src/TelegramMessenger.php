<?php
namespace TurboLabIt\Messengers;

use stdClass;
use Symfony\Component\HttpFoundation\Request;


/**
 * 📚 https://core.telegram.org/bots/api#making-requests
 */
class TelegramMessenger extends BaseMessenger
{
    const SERVICE_NAME  = self::SERVICE_TELEGRAM;
    const ENDPOINT      = 'https://api.telegram.org/bot';
    const WEBURL        = 'https://t.me/';

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
        return $this->sendMessage($this->getEnvTag() . $message, array_merge([
            "chat_id" => $this->arrConfig["Telegram"]["channelId"],
        ], $arrParams));
    }


    public function sendErrorMessage(string $message, array $arrParams = [], ?string $emoji = '🛑') : stdClass
    {
        $fullMessage = $this->getEnvTag(true);

        if( !empty($emoji) ) {
            $fullMessage .= "$emoji ";
        }

        $fullMessage .= $message;

        return $this->sendMessage($fullMessage, array_merge([
            "chat_id" => $this->arrConfig["Telegram"]["errorsChannelId"],
        ], $arrParams));
    }


    protected function getEnvTag(bool $includeProd = false) : string
    {
        $envTag = parent::getEnvTag($includeProd);
        $envTag = trim($envTag);
        if( empty($envTag) ) {
            return $envTag;
        }

        return "<b>{$envTag}</b> ";
    }


    public function sendMessage(string $message, array $arrParams) : stdClass
    {
        // 📚 https://core.telegram.org/bots/api#sendmessage

        $arrParams = array_merge_recursive([
            "text"                      => $this->messageEncoder($message),
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


    protected function messageEncoder(string $message) : string
    {
        /**
         * this function handles an issue with some entities, as &apos;
         * which are shown as-they-are on delivered messages
         * 📚 https://core.telegram.org/bots/api#formatting-options
         * 
         * All <, > and & symbols that are not a part of a tag or an HTML entity
         * must be replaced with the corresponding HTML entities 
         * (< with &lt;, > with &gt; and & with &amp;).
         * 
         * The API currently supports only the following named HTML entities: 
         * &lt;, &gt;, &amp; and &quot;.
         */

        $arrEntitiesToProtect = [
            '&lt;'      => 'LT',
            '&gt;'      => 'GT',
            '&amp;'     => 'AMP',
            '&quot;'    => 'QUOT',
        ];

        $protectorString = uniqid();

        foreach($arrEntitiesToProtect as &$val) {
            $val = "{$protectorString}_{$val}_{$protectorString}";
        }

        $protectedMessage =
            str_ireplace(array_keys($arrEntitiesToProtect), $arrEntitiesToProtect, $message);

        $encodedProtectedMessage = html_entity_decode($protectedMessage, ENT_QUOTES | ENT_HTML5, "UTF-8");
        
        $finalMessage =
            str_ireplace($arrEntitiesToProtect, array_keys($arrEntitiesToProtect), $encodedProtectedMessage);

        return $finalMessage;
    }


    protected function apiCall(string $endPoint, array $arrParam, string $method = Request::METHOD_POST, array $arrHeaders = []) : stdClass
    {
        $this->lastResponse = $this->httpClient->request($method, $endPoint, [
            "headers"   => $arrHeaders,
            "query"     => $arrParam
        ]);

        $content = $this->lastResponse->getContent(false);

        if( empty($content) ) {
            throw new TelegramException("Empty response from the Telegram endpoint");
        }

        $oJson = json_decode($content);

        if( empty($oJson) ) {
            throw new TelegramException("JSON response decoding error");
        }

        if( ( $oJson->ok ?? false ) != true ) {
            throw new TelegramException("the Telegram endpoint returned an error: " . ($oJson->description ?? '<no error>'));
        }

        return $oJson;
    }


    public function getChannelUrl() : string
    {
        return static::WEBURL . '/s/' . trim($this->arrConfig["Telegram"]["channelId"], '@');
    }


    public function buildNewMessageUrl(stdClass $oJsonResponse) : string
    {
        return static::WEBURL . trim($this->arrConfig["Telegram"]["channelId"], '@') . "/" . $oJsonResponse->result->message_id;
    }
}
