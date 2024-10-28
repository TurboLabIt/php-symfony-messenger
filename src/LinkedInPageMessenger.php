<?php
namespace TurboLabIt\MessengersBundle;


use Symfony\Component\HttpFoundation\Response;

class LinkedInPageMessenger extends BaseMessenger
{
    const AUTH_CODE_FILENAME        = 'linkedin_auth_code.json';
    const TOKEN_RESPONSE_FILENAME   = 'linkedin_token_response.json';


    public function getAuthCodeUrl(string $returnToUrl) : string
    {
        $clientId       = $this->arrConfig['LinkedIn']['clientId'];
        $returnToUrl    = urlencode($returnToUrl);

        return
            "https://www.linkedin.com/oauth/v2/authorization?response_type=code&" .
                "client_id=$clientId&redirect_uri=$returnToUrl&scope=w_member_social";
    }


    public function storeAuthCode(string $code, string $returnToUrl) : static
    {
        $authCodeFilePath = $this->getAuthCodeFilePath();
        $oAuthCode = (object)[
            "code"  =>  $code,
            "date"  => (new \DateTime())->format('Y-m-d H:i:s'),
            "url"   => $returnToUrl
        ];

        $jsonAuthCode = json_encode($oAuthCode, JSON_PRETTY_PRINT);
        file_put_contents($authCodeFilePath, $jsonAuthCode);

        return $this;
    }


    protected function getAuthCodeFilePath() : string { return $this->getVarDirPath(static::AUTH_CODE_FILENAME); }


    public function exchangeAuthCodeForToken(string $code, string $returnToUrl) : static
    {
        $this->lastResponse =
            $this->httpClient->request('POST', 'https://www.linkedin.com/oauth/v2/accessToken', [
                "body"  => [
                    "grant_type"    => "authorization_code",
                    "code"          => $code,
                    "redirect_uri"  => $returnToUrl,
                    "client_id"     => $this->arrConfig['LinkedIn']['clientId'],
                    "client_secret" => $this->arrConfig['LinkedIn']['clientSecret']
                ]
            ]);

        $content = $this->lastResponse->getContent(false);

        if( empty($content) || $this->lastResponse->getStatusCode() != Response::HTTP_OK ) {
            throw new LinkedInException("LinkedIn oAuth error: $content");
        }

        $oJson = json_decode($content);
        foreach(['access_token', 'expires_in', 'scope'] as $key) {

            if( empty($oJson->$key) ) {
                throw new LinkedInException("LinkedIn oAuth error: ##$key## is empty");
            }
        }

        $tokenResponseFilePath = $this->getTokenResponseFilePath();
        file_put_contents($tokenResponseFilePath, $content);

        return $this;
    }


    protected function getTokenResponseFilePath() : string { return $this->getVarDirPath(static::TOKEN_RESPONSE_FILENAME); }
}
