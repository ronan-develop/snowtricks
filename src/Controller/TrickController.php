<?php

namespace App\Controller;

use DateTimeZone;
use App\Entity\Trick;
use Twig\Environment;
use DateTimeImmutable;
use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
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
    public function index(TrickRepository $trickRepository, CommentRepository $commentRepository, UserRepository $userRepository, Request $request): Response
    {
        $slug = $request->get('slug');
        $trick = $trickRepository->findOneBy(['slug'=>$slug]);

        if ($this->getUser()) {
            $currentUser = $this->getUser()->getUserIdentifier();
            $user = $userRepository->findOneBy(['username' => $currentUser]);

            $comment = new Comment($trick);
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
                'user' => $this->getUser()->getUserIdentifier(),
                'comments' => $commentRepository->findBy(['trick'=>$trick])
            ]);
        }
        return $this->render('trick/index.html.twig', [
            'trick' => $trick,
            'comments' => $commentRepository->findBy(['trick' => $trick])
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

        //todo retirer les commentaires + try catch

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

    #[Route('see-medias/{slug}', name: 'app_media')]
    public function featured(Request $request, TrickRepository $trickRepository)
    {
        $slug = $request->get('slug');
        $trick = $trickRepository->findOneBy(['slug'=> $slug]);

        return $this->render('trick/medias.html.twig', [
            'trick' => $trick
        ]);
    }
}
