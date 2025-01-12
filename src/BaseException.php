<?php
namespace TurboLabIt\MessengersBundle;

use stdClass;


class BaseException extends \Exception
{
    protected ?string $response         = null;
    protected ?stdClass $jsonReponse    = null;


    public function getResponse(): ?string { return $this->response; }

    public function setResponse(?string $response) : static
    {
        $this->response     = $response;
        $this->jsonReponse  = new stdClass();

        if( !empty($response) && is_string($response) ) {

            $oJson = json_decode($response);
            $this->jsonReponse = $oJson instanceof stdClass ? $oJson : $this->jsonReponse;
        }

        return $this;
    }


    public function getJsonReponse(): stdClass
    {
        if( empty($this->jsonReponse) ) {
            return new stdClass();
        }

        return $this->jsonReponse;
    }
}
