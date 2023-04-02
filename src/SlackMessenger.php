<?php
/**
 * @see https://github.com/TurboLabIt/php-symfony-messenger
 */
namespace TurboLabIt\Messengers;


class SlackMessenger extends AbstractBaseMessenger
{
    public function sendMessageToChannel(string $message, ?string $channel = null)
    {
        $channel = $channel ?? $this->arrConfig["Slack"]["channel"];

        $this->response =
            $this->httpClient->request(
                'POST',
                $this->arrConfig["Slack"]["endpoint"], [
                    'json' => [
                        "channel"   => $channel,
                        "text"      => $message
                    ]
                ]
            );

        $content = $this->response->getContent();
        return $content;
    }
}
