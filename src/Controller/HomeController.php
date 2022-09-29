<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(TrickRepository $trickRepository, Session $session): Response
    {
        $message = null;

        foreach ($session->getFlashBag()->get('warning', []) as $message) {
            echo '<div class="flash-warning">'.$message.'</div>';
        }
        
        $tricks = $trickRepository->findAll();
        return $this->render('home/index.html.twig', [
            'tricks' => $tricks,
            'message' => $message
        ]);
    }
}
