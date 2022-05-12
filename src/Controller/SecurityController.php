<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_security_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        //if ($this->getUser()) {
        // return $this->redirectToRoute('target_path');
        //}

        //$form = $this->createForm(LoginType::class);

        //J'appelle getLastAuthentificatorError qui va retrouver la clé dans la fonction -utils) et me retourner l'AuthenticationError
        $error = $authenticationUtils->getLastAuthenticationError();
        
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 'error' => $error]);
        // return $this->render('security/login.html.twig', [ 
        // 'formView' => $form->createView()
        // ]);
    }

    #[Route(path: '/logout', name: 'app_security_logout')]
    public function logout(): void
    {
    //     throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}