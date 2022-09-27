<?php

namespace App\Controller;

use DateTimeZone;
use App\Entity\Trick;
use Twig\Environment;
use DateTimeImmutable;
use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    public function __construct(private Environment $twig, private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/trick/{slug}', name: 'app_trick')]
    public function index(TrickRepository $trickRepository, Request $request): Response
    {
        $slug = $request->get('slug');
        $trick = $trickRepository->findOneBy(['slug'=>$slug]);

        if ($this->getUser()) {
            $user = $this->getUser();
            $comment = new Comment();
            $form = $this->createForm(CommentFormType::class, $comment);
            $form->handleRequest($request);
            $date_comment = $comment->getCreatedAt();

            if ($form->isSubmitted() && $form->isValid()) {
                $comment
                ->setCreatedAt(new DateTimeImmutable("now", new DateTimeZone('Europe/Paris')))
                ->setUser($user);

                $this->entityManager->persist($comment);
                $this->entityManager->flush();
            }
            return $this->render('trick/index.html.twig', [
                'trick' => $trick,
                'categories' =>  $trick->getCategory(),
                'comment_form' => $form->createView(),
                'date_comment' => $date_comment,
                'user' => $this->getUser()->getUserIdentifier()
            ]);
        }
        return $this->render('trick/index.html.twig', [
            'trick' => $trick,
        ]);
    }

    #[Route('/tricks', name: 'app_tricks')]
    public function tricks(TrickRepository $trickRepository, Request $request): Response
    {
        $tricks = $trickRepository->findAll();

        return $this->render('tricks/index.html.twig', [
            'tricks' => $tricks,
        ]);
    }

    #[Route('/tricks/{slug}/delete', name: 'app_delete_trick')]
    public function delete(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $slug = $request->get('slug');

        $repo = $em->getRepository(Trick::class);

        $trick = $repo->findOneBy(['slug' => $slug]);

        //todo retirer les commentaires try catch

        // not connected
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'code' => 403,
                'message' =>  'Unauthorized'
            ], 403);
        }

        $em->remove($trick);
        $em->flush();

        return $this->json(['code' => 200, 'name' => $trick->getName(), 'message' => 'la suppression s\'est bien déroulée'], 200);
    }

    #[Route('modify/{slug}/featured', name: 'app_featured')]
    public function featured(Request $request)
    {
        
    }
}
