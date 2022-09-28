<?php

namespace App\Controller;

use DateTimeZone;
use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Comment;
use App\Repository\UserRepository;
use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @method User getUser()
 */
class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment')]
    public function add(
        Request $request,
        TrickRepository $trickRepository,
        CommentRepository $commentRepository,
        EntityManagerInterface $em
    ): Response {
        $commentData = $request->request->all('comment_form');

        if (!$this->isCsrfTokenValid('comment-add', $commentData['_token'])) {
            // dd($commentData);
            return $this->json([
                'code' => 'INVALID_CSRF_TOKEN'
            ], Response::HTTP_BAD_REQUEST);
        }

        $trick = $trickRepository->findOneBy(['id' => $commentData['trick']]);

        if (!$trick) {
            return $this->json([
                'code' => 'ARTICLE_NOT_FOUND'
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'code' => 'USER_NOT_AUTHENTICATED_FULLY'
            ], Response::HTTP_BAD_REQUEST);
        }

        $comment = new Comment($trick);
        $comment->setContent($commentData['content']);
        $comment->setCreatedAt(new DateTimeImmutable("now", new DateTimeZone('Europe/Paris')));
        $comment->setUser($user);
        $em->persist($comment);
        $em->flush();

        $html = $this->renderView('comment/index.html.twig', [
            'comment' => $comment
        ]);

        return $this->json([
            'code' => 'COMMENT_ADDED_SUCCESSFULLY',
            'message' => $html,
            'number' => $commentRepository->count(['trick' => $trick])
        ]);
    }
}
