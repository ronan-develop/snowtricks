<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(UserRepository $userRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_profile');
        }

        $user = $userRepository->findOneBy([
            'username' => $this->getUser()->getUserIdentifier()
        ]);

        $tricks = $user->getTricks();

        return $this->render('account/index.html.twig', [
            'tricks' => $tricks,
            'user_name' => $user->getUsername()
        ]);
    }
}
