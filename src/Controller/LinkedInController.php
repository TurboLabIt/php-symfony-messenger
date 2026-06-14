<?php
namespace TurboLabIt\MessengersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use TurboLabIt\MessengersBundle\LinkedInPageMessenger;


/**
 * 📚 https://github.com/TurboLabIt/php-symfony-messenger/blob/main/docs/linkedin.md
 */
class LinkedInController extends AbstractController
{
    const ROUTE_PATH            = '/setup/linkedin/auth/';
    const ROUTE_NAME_START      = 'turbolabit_messengers_linkedin_auth_start';
    const ROUTE_NAME_RETURN_TO  = 'turbolabit_messengers_linkedin_auth_code-return-to';


    public function __construct(
        protected LinkedInPageMessenger $messenger, protected UrlGeneratorInterface $urlGenerator,
        protected ?Security $security = null
    ) {}


    #[Route(self::ROUTE_PATH, name: self::ROUTE_NAME_START)]
    public function linkedInAuth() : Response
    {
        $this->enforceAuthorization();

        $returnToUrl = $this->urlGenerator->generate(self::ROUTE_NAME_RETURN_TO, [], UrlGeneratorInterface::ABSOLUTE_URL);
        $linkedInAuthUrl = $this->messenger->getAuthCodeUrl($returnToUrl);
        return new Response('<a href="' . $linkedInAuthUrl . '">Start LinkedIn auth</a>');
    }


    #[Route(self::ROUTE_PATH . 'code-return-to/', name: self::ROUTE_NAME_RETURN_TO)]
    public function linkedAuthCodeReturnTo(Request $request) : Response
    {
        $this->enforceAuthorization();

        $code = $request->query->get('code');

        if( empty($code) ) {
            throw new BadRequestHttpException('Invalid LinkedIn code');
        }

        $returnToUrl = $this->urlGenerator->generate(self::ROUTE_NAME_RETURN_TO, [], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->messenger->exchangeAuthCodeForToken($code, $returnToUrl);

        return new Response('DONE');
    }


    protected function enforceAuthorization() : void
    {
        $arrRequiredRoles = $this->messenger->getOAuthRequiredRoles();

        if( empty($arrRequiredRoles) ) {
            return;
        }

        foreach($arrRequiredRoles as $role) {
            if( $this->security->isGranted($role) ) {
                return;
            }
        }

        throw new AccessDeniedHttpException();
    }
}
