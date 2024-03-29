<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthentificatorAuthenticator extends AbstractLoginFormAuthenticator {
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'login';

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator) {
        $this->urlGenerator = $urlGenerator;
    }

    public function authenticate(Request $request): PassportInterface {
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge(
                    'authenticate',
                    $request->request->get('_csrf_token')
                ),
            ]
        );
    }

    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): ?Response {
        // If the param redirect_to is set, redirect to this path.
        // Otherwise redirect to the homepage.
        $redirectTo = $request->query->get('redirect_to');
        if ($redirectTo) {
            return new RedirectResponse($redirectTo);
        }

        $roles = $token->getUser()->getRoles();

        if (in_array("ROLE_SUPER_ADMIN", $roles)) {
            return new RedirectResponse(
                $this->urlGenerator->generate('admin')
            );
        } elseif (in_array("ROLE_ADMIN_FRANCHISE", $roles)) {
            return new RedirectResponse(
                $this->urlGenerator->generate('franchise_kpi')
            );
        } elseif (in_array("ROLE_ADMIN_SALLE", $roles)) {
            return new RedirectResponse(
                $this->urlGenerator->generate('gym_kpi')
            );
        }
        // For example:
        return new RedirectResponse(
            $this->urlGenerator->generate('accueil')
        );
        throw new \Exception(
            'TODO: provide a valid redirect inside ' . __FILE__
        );
    }

    protected function getLoginUrl(Request $request): string {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}