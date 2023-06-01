<?php
/**
 * @see https://github.com/TurboLabIt/php-symfony-messenger
 */
namespace TurboLabIt\Messengers;


use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpFoundation\Response;

class SlackMessenger extends AbstractBaseMessenger
{
    public function sendMessageToChannel(string $message,)
    {
        return $this->makeRequest(
            $this->arrConfig["Slack"]["endpoints"]["main"], [
                'json' => [
                    "text" => $message
                ]
            ]
        );
    }


    public function sendErrorMessage(string $message)
    {
        return $this->makeRequest(
            $this->arrConfig["Slack"]["endpoints"]["errors"], [
                'json' => [
                    "text" => $message
                ]
            ]
        );
    }


    protected function makeRequest(string $endpoint, array $arrParam, string $method = 'POST')
    {
        $endpoint = $this->prepareEndpoint($endpoint, true, false);

        $this->response = $this->httpClient->request($method, $endpoint, $arrParam);

        $txtResponse = $this->response->getContent();

        return $txtResponse;
    }
}
