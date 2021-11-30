<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class DashboardsAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private $security;
    protected $authorizationCheckerInterface;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator, Security $security, AuthorizationCheckerInterface $authorizationCheckerInterface)
    {
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
        $this->authorizationCheckerInterface = $authorizationCheckerInterface;
    }

    public function authenticate(Request $request): PassportInterface
    {
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        $user = $this->security->getUser();
        $roleSuperAdmin = $this->authorizationCheckerInterface->isGranted("ROLE_SUPER_ADMIN", $user);
        $roleAdmin = $this->authorizationCheckerInterface->isGranted("ROLE_ADMIN", $user);
        $roleAssoc = $this->authorizationCheckerInterface->isGranted("ROLE_ASSOC", $user);
        $roleAdherent = $this->authorizationCheckerInterface->isGranted("ROLE_ADHERENT", $user);

        if ($roleSuperAdmin) {
            return new RedirectResponse($this->urlGenerator->generate('dashboards_superadmin_home'));
        } elseif ($roleAdmin || $roleAssoc) {
            return new RedirectResponse($this->urlGenerator->generate('dashboards_admin_home'));
        } elseif ($roleAdherent) {
            return new RedirectResponse($this->urlGenerator->generate('dashboards_adherent_home'));
        }


        // For example:
        //return new RedirectResponse($this->urlGenerator->generate('api_v1_docs_superadmin'));
        //throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
