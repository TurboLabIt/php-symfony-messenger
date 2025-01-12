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
        if( $this->isExpiredToken() && !empty($renewalUrl) ) {
            $this->message .= PHP_EOL . "Renew at $renewalUrl";
        }

        return $this;
    }
}
