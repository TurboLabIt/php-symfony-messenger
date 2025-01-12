<?php
namespace TurboLabIt\MessengersBundle;

class LinkedInException extends BaseException
{
    public function isExpiredToken() :bool
    {
        $code   = $this->getJsonReponse()->code ?? null;
        return $code == 'EXPIRED_ACCESS_TOKEN';
    }


    public function setResponse(?string $response) : static
    {
        parent::setResponse($response);
        return $this;
    }


    public function addTokenRenewalUrlToMessage(?string $renewalUrl = null) : static
    {
        $this->message .= PHP_EOL . "Get a token from $renewalUrl";
        return $this;
    }
}
