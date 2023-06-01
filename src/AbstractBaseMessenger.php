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


    protected function prepareEndpoint(
        string $endpoint, bool $removeTrailingSlash = true, bool $addTrailingSlash = true, 
        string $endpointAction = '', array $arrQueryStringParam = []
    ) : string
    {
        if($removeTrailingSlash) {
            $endpoint = rtrim($endpoint, '/');
        }
        
        if($addTrailingSlash) {
            $endpoint .= '/';
        }
        
        $endpoint .= $endpointAction;

        if( empty($arrQueryStringParam) ) {
            return $endpoint;
        }

        if( stripos($endpoint, '?') === false ) {
            $endpoint .= '?';
        }

        $endpoint .= http_build_query($arrQueryStringParam);

        return $endpoint;
    }
}
