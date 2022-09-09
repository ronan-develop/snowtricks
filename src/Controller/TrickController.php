<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    #[Route('/trick/{slug}', name: 'app_trick')]
    public function index(TrickRepository $trickRepository, Request $request): Response
    {
        $slug = $request->get('slug');
        $trick = $trickRepository->findOneBy(['slug'=>$slug]);
        
        return $this->render('trick/index.html.twig', [
            'trick' => $trick,
        ]);
    }
}
