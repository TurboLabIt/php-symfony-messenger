<?php
namespace TurboLabIt\Messengers;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;


abstract class BaseMessenger
{
    protected ?ResponseInterface $lastResponse = null;

    public function __construct(protected array $arrConfig, protected HttpClientInterface $httpClient) {}
}
