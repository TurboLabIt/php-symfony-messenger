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
    const ROUTE_PATH = '/setup/linkedin/auth/';

    public function __construct(
        protected LinkedInPageMessenger $messenger, protected UrlGeneratorInterface $urlGenerator,
        protected Security $security
    ) {}


    #[Route(self::ROUTE_PATH, name: 'turbolabit_messengers_linkedin_auth_start')]
    public function linkedInAuth() : Response
    {
        $this->enforceAuthorization();

        $returnToUrl = $this->getCodeReturnToUrl();
        $linkedInAuthUrl = $this->messenger->getAuthCodeUrl($returnToUrl);
        return new Response('<a href="' . $linkedInAuthUrl . '">Start LinkedIn auth</a>');
    }


    protected function getCodeReturnToUrl() : string
    {
        return
            $this->urlGenerator->generate(
                'turbolabit_messengers_linkedin_auth_code-return-to', [],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
    }


    #[Route(self::ROUTE_PATH . 'code-return-to/', name: 'turbolabit_messengers_linkedin_auth_code-return-to')]
    public function linkedAuthCodeReturnTo(Request $request) : Response
    {
        $this->enforceAuthorization();

        $code = $request->get('code');

        if( empty($code) ) {
            throw new BadRequestHttpException('Invalid LinkedIn code');
        }

        $returnToUrl = $this->getCodeReturnToUrl();

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
