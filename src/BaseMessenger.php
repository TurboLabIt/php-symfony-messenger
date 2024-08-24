<?php
namespace TurboLabIt\Messengers;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


abstract class BaseMessenger
{
    protected ?ResponseInterface $lastResponse = null;

    public function __construct(
        protected array $arrConfig, protected HttpClientInterface $httpClient,
        protected ParameterBagInterface $parameters 
    ) {}


    protected function getEnvTag(bool $includeProd = false) : string
    {
        $env = $this->parameters->get("kernel.environment");
        
        if( $env == 'prod' && !$includeProd ) {
            return '';
        }
        
        return "[" . strtoupper($env) . "] ";
    }
}
