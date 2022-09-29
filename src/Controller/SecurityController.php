<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Session $session): Response
    {
        if ($this->getUser()) {
            $session->getFlashBag()->add(
                'warning',
                'Vous êtes déjà connecté sous le pseudo '. $this->getUser()->getUserIdentifier()
            );

            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        
        if ($error) {
            $session->getFlashBag()->add(
                'warning',
                $error->getMessage()
            );
            foreach ($session->getFlashBag()->get('warning', []) as $message) {
                echo '<div class="flash-warning">'.$message.'</div>';
            }
        }


        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
