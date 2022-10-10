<?php

namespace App\Controller;

use App\Entity\Trick;
use Twig\Environment;
use App\Form\CommentFormType;
use App\Services\CommentService;
use App\Repository\UserRepository;
use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TrickController extends AbstractController
{
    public function __construct(
        private Environment $twig,
        private EntityManagerInterface $entityManager,
        private TrickRepository $trickRepository
    ) {
    }

    #[Route('/trick/{slug}', name: 'app_trick')]
    public function index(
        Trick $trick,
        TrickRepository $trickRepository,
        CommentRepository $commentRepository,
        Request $request,
        PaginatorInterface $paginator,
        EntityManagerInterface $em
    ): Response {
        $slug = $request->get('slug');
        
        $trick = $trickRepository->findOneBy(['slug' => $slug]);

        if (!$trick) {
            throw $this->createNotFoundException('Trick '. $slug . ' non trouvé');
        }

        $data = $commentRepository->findBy(['trick' => $this->trickRepository->findOneBy([
            'slug' => $slug,
        ])],
        ['createdAt' => 'desc']
        );

        $comments = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        if ($this->getUser()) {

            $commentService = new CommentService(
                $this->getUser(),
                $slug,
                $this->trickRepository,
                $em
            );

            $form = $this->createForm(CommentFormType::class, $commentService->comment);

            $form->handleRequest($request);
            
            if($form->isSubmitted() && $form->isValid()){

                try {
                    $commentService->handleForm();
                    $this->addFlash('success', 'Commentaire correctement ajouté');
                } catch (Exception $e) {
                    $this->addFlash(
                        'error', 'oops! quelque chose s\'est mal passé sur le serveur : '.$e->getMessage()
                    );
                }
            }

            return $this->render('trick/index.html.twig', [
                'trick' => $trick,
                'categories' =>  $trick->getCategory(),
                'comment_form' => $form->createView(),
                'user' => $this->getUser()->getUserIdentifier(),
                'comments' => $comments

            ]);
        }
        return $this->render('trick/index.html.twig', [
            'trick' => $trick,
            'comments' => $commentRepository->findBy(['trick' => $trick]),
            'date_comment' => '',
            'comments' => $comments
        ]);
    }

    #[Route('/tricks/{slug}/delete', name: 'app_delete_trick')]
    public function delete(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $slug = $request->get('slug');

        $repo = $em->getRepository(Trick::class);

        $trick = $repo->findOneBy(['slug' => $slug]);

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
