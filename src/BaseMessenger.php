<?php
namespace TurboLabIt\MessengersBundle;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


abstract class BaseMessenger
{
    const VAR_DIR_NAME      = 'messenger';

    const SERVICE_TELEGRAM  = 'telegram';
    const SERVICE_FACEBOOK  = 'facebook';
    const SERVICE_TWITTER   = 'twitter';
    const SERVICE_X         = 'x';
    const SERVICE_LINKEDIN  = 'linkedin';

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
        { return html_entity_decode($message, ENT_QUOTES | ENT_HTML5, "UTF-8"); }


    protected function getVarDirPath(?string $filename = null) : string
    {
        $varDirFilePath =
            $this->parameters->get('kernel.project_dir') . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR .
            static::VAR_DIR_NAME . DIRECTORY_SEPARATOR;

        if( !is_dir($varDirFilePath) ) {
            mkdir($varDirFilePath, 0775);
        }

        if( empty($filename) ) {
            return $varDirFilePath;
        }

        return $varDirFilePath . $filename;
    }
}
