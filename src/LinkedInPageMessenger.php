<?php
namespace TurboLabIt\MessengersBundle;

use Symfony\Component\HttpFoundation\Response;
use TurboLabIt\MessengersBundle\Controller\LinkedInController;


/**
 * 📚 https://github.com/TurboLabIt/php-symfony-messenger/blob/main/docs/linkedin.md
 */
class LinkedInPageMessenger extends BaseMessenger
{
    const SERVICE_NAME              = self::SERVICE_LINKEDIN;
    const TOKEN_RESPONSE_FILENAME   = 'linkedin_token_response.json';
    const API_VERSION               = '202401';
    const WEBURL                    = 'https://www.linkedin.com/';

    protected array $arrPermissions = ["w_organization_social"];


    public function getOAuthRequiredRoles() : array { return  $this->arrConfig["LinkedIn"]["oAuthRequiredRoles"]; }


    public function getAuthCodeUrl(string $returnToUrl) : string
    {
        $clientId       = $this->arrConfig['LinkedIn']['clientId'];
        $returnToUrl    = urlencode($returnToUrl);

        return "https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=$clientId&" .
                    "redirect_uri=$returnToUrl&scope=" . implode('%20', $this->arrPermissions);
    }


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

        $tokenResponseFilePath = $this->getVarDirPath(static::TOKEN_RESPONSE_FILENAME);
        file_put_contents($tokenResponseFilePath, json_encode($oJson, JSON_PRETTY_PRINT));
        return $this;
    }


    protected function getTokenResponseFromFile() : \stdClass
    {
        $path = $this->getVarDirPath(static::TOKEN_RESPONSE_FILENAME);
        if( !file_exists($path) ) {
            throw new LinkedInException("The Bearer token file ##$path## does not exist");
        }

        $content = file_get_contents($path);

        if( empty($content) ) {
            throw new LinkedInException("The Bearer token file ##$path## is empty");
        }

        $oJson = json_decode($content);

        if( empty($oJson->access_token) ) {
            throw new LinkedInException("The Bearer token file ##$path## doesn't decode");
        }

        return $oJson;
    }


    public function sendUrl(string $title, string $url, ?string $imageUrl = null) : ?string
    {
        $title = $this->messageEncoder($title);
        $isProd = $this->parameters->get("kernel.environment") == "prod";

        // https://learn.microsoft.com/en-us/linkedin/marketing/community-management/shares/posts-api?tabs=http#article-post-creation-sample-request
        $oMessage = (object)[
            "author"            => "urn:li:organization:" . $this->arrConfig['LinkedIn']['organizationId'],
            "commentary"        => $this->getEnvTag(false),
            "visibility"        => $isProd ? "PUBLIC" : "LOGGED_IN",
            "distribution"      => (object)[
                "feedDistribution"  => $isProd ? "MAIN_FEED" : "NONE",
            ],
            "content"           => (object)[
                "article"   => (object)[
                    "source"    => $url,
                    "thumbnail" => empty($imageUrl) ? null : $this->uploadImage($imageUrl),
                    "title"     => $title
                ]
            ],
            "contentLandingPage"=> $url,
            "lifecycleState"    => "PUBLISHED"
        ];

        if( empty($oMessage->content->article->thumbnail) ) {
            unset($oMessage->content->article->thumbnail);
        }

        $this->lastResponse =
            $this->httpClient->request('POST', 'https://api.linkedin.com/rest/posts', [
                "headers"   => $this->getRequestHeader(),
                "json"      => $oMessage
            ]);

        $content = $this->lastResponse->getContent(false);
        $httpStatusCode = $this->lastResponse->getStatusCode();

        if( $httpStatusCode != Response::HTTP_CREATED ) {
            throw new LinkedInException("The LinkedIn endpoint returned an HTTP $httpStatusCode error: $content");
        }

        $arrHeaderId = $this->lastResponse->getHeaders(false)["x-restli-id"] ?? null;
        return empty($arrHeaderId[0]) ? null : $arrHeaderId[0];
    }


    public function uploadImage(string $imageUrl) : string
    {
        // https://learn.microsoft.com/en-us/linkedin/marketing/community-management/shares/images-api
        $oMessage = (object)[
            "initializeUploadRequest" => (object)[
                "owner" => "urn:li:organization:" . $this->arrConfig['LinkedIn']['organizationId']
            ]
        ];

        $this->lastResponse =
            $this->httpClient->request('POST', 'https://api.linkedin.com/rest/images?action=initializeUpload', [
                "headers"   => $this->getRequestHeader(),
                "json"      => $oMessage
            ]);

        $content = $this->lastResponse->getContent(false);
        $httpStatusCode = $this->lastResponse->getStatusCode();

        if( $httpStatusCode != Response::HTTP_OK ) {

            $renewalUrl = $this->getAuthStartUrl();

            throw
                (new LinkedInException(
                    "The LinkedIn images endpoint returned an HTTP $httpStatusCode error: $content", $httpStatusCode
                ))
                ->setResponse($content)
                ->addTokenRenewalUrlToMessage($renewalUrl);
        }

        $oJsonResponse = json_decode($content);

        if( empty($oJsonResponse->value->uploadUrl) || empty($oJsonResponse->value->image) ) {
            throw new LinkedInException("The LinkedIn images endpoint didn't provide the upload URL");
        }

        $imageUrn = $oJsonResponse->value->image;

        // https://learn.microsoft.com/en-us/linkedin/marketing/community-management/shares/vector-asset-api?view=li-lms-2024-10#upload-the-image
        $this->lastResponse =
            $this->httpClient->request('PUT', $oJsonResponse->value->uploadUrl, [
                "headers" => array_merge($this->getRequestHeader(), [
                    'Content-Type' => '',
                ]),
                "body" => file_get_contents($imageUrl)
            ]);

        $content = $this->lastResponse->getContent(false);
        $httpStatusCode = $this->lastResponse->getStatusCode();

        if( $httpStatusCode != Response::HTTP_CREATED ) {
            throw new LinkedInException("The LinkedIn image upload endpoint returned an HTTP $httpStatusCode error: $content");
        }

        return $imageUrn;
    }


    public function buildNewMessageUrl(string $postUrn) : string
    {
        $lastColonPos   = strrpos($postUrn, ":");
        $messageId      = substr($postUrn, $lastColonPos + 1);
        return static::WEBURL . 'feed/update/urn:li:share:' . $messageId;
    }


    protected function getRequestHeader() : array
    {
        return [
            "Authorization"             => "Bearer " . $this->getTokenResponseFromFile()->access_token,
            // Ensure you include the request header "Content-Type": "application/json"
            "Content-Type"              => "application/json",
            // All APIs require the request header LinkedIn-Version: {Version in YYYYMM format}.
            "LinkedIn-Version"          => static::API_VERSION,
            // All APIs require the request header
            "X-Restli-Protocol-Version" => "2.0.0"
        ];
    }


    protected function getAuthStartUrl() : string
    {
        $baseUrl =
            $this->parameters->has('router.request_context.scheme') ?
                $this->parameters->get('router.request_context.scheme') : 'https';

        $baseUrl .= '://';

        $baseUrl .=
            $this->parameters->has('router.request_context.host') ?
                $this->parameters->get('router.request_context.host') : 'example.com';

        $baseUrl = rtrim($baseUrl, '/') . LinkedInController::ROUTE_PATH;
        return $baseUrl;
    }
}
