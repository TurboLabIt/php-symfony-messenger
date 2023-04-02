<?php
/**
 * @see https://github.com/TurboLabIt/php-symfony-messenger
 */
namespace TurboLabIt\Messengers;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;


abstract class AbstractBaseMessenger
{
    protected ResponseInterface $response;


    public function __construct(protected array $arrConfig, protected HttpClientInterface $httpClient)
    { }
}
