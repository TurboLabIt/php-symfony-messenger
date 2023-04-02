<?php
/**
 * @see https://github.com/TurboLabIt/php-symfony-messenger
 */
namespace TurboLabIt\Messengers;


class SlackMessenger extends AbstractBaseMessenger
{
    public function sendMessageToChannel(string $message,)
    {
        $this->response =
            $this->httpClient->request(
                'POST',
                $this->arrConfig["Slack"]["endpoints"]["main"], [
                    'json' => [
                        "text"      => $message
                    ]
                ]
            );

        $content = $this->response->getContent();
        return $content;
    }
}
