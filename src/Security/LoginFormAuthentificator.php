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
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthentificator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_security_login';

    private UrlGeneratorInterface $urlGenerator;
    
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    
    //Passeport est une class Symfony qui va contenir les infos ayant besoin d'être validées durant le workflow d'authentification
    public function authenticate(Request $request): Passport

   
    { 
        //Dans ma request je vérifie les info de connexion
        $email = $request->request->get('email', '');
        //Grâce à l'email je retrouve dans le bon dossier dans la bdd
        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            //Un badge transporte et ajoute les infos de l'user au passeport
            new UserBadge($email),
            //credentials = information de connexion
            //On vérifie si le mdp correspond bien à l'email
            new PasswordCredentials($request->request->get('password', '')),
            [
                //Injecte un jeton de sécurité
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                
            ]
            
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
      
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

            return new RedirectResponse($this->urlGenerator->generate('homepage'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}