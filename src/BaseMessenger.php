<?php
namespace TurboLabIt\Messengers;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


abstract class BaseMessenger
{
    const SERVICE_TELEGRAM  = 'telegram';
    const SERVICE_FACEBOOK  = 'facebook';
    const SERVICE_TWITTER   = 'twitter';
    const SERVICE_X         = 'x';
    
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


    protected function messageEncoder(string $message) : string
    {
        $finalMessage =
            html_entity_decode($message, ENT_QUOTES | ENT_HTML5, "UTF-8");

        return $finalMessage;
    }
}
