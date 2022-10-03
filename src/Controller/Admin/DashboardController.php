<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\Category;
use App\Repository\UserRepository;
use App\Repository\TrickRepository;
use App\Controller\Admin\TrickCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator,
        private UserRepository $userRepository,
        private TrickRepository $trickRepository
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        /**
         * account not verified
         */
        $user = $this->userRepository->findOneBy(['username'=>$this->getUser()->getUserIdentifier()]);

        if ($user->getIsVerified() === false) {
            $this->addFlash('warning', 'Vous devez confirmer votre compte, relevez vos emails (😉 dans le doute, vérifiez vos SPAMS)');

            return $this->render('home/index.html.twig', [
                'tricks'=> $this->trickRepository->findAll()
            ]);
        }

        $url = $this->adminUrlGenerator->setController(TrickCrudController::class)
        ->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Snowtricks')
            ->renderSidebarMinimized();
    }

    /**
     * set items in left menu
     *
     * @return iterable
     */
    public function configureMenuItems(): iterable
    {
        /**
         * account not verified
         */
        $user = $this->userRepository->findOneBy(['username'=>$this->getUser()->getUserIdentifier()]);

        if ($user->getIsVerified() === false) {
            $this->addFlash('warning', 'Vous devez confirmer votre compte, relevez vos emails (😉 dans le doute, vérifiez vos SPAMS)');
            return [
                yield MenuItem::subMenu('', 'fa-solid fa-arrow-rotate-left')->setSubItems([
                    MenuItem::linkToUrl('retour au site', '', $this->generateUrl('app_home')),
                ]),
            ];
        }

        /**
         * Admin is user too -> remove link to 'Gestion de mon compte'
         */
        if ($this->isGranted('ROLE_USER') && !$this->isGranted('ROLE_ADMIN')) {

            // back to home
            yield MenuItem::section('Navigation');
            yield MenuItem::subMenu('', 'fa-solid fa-arrow-rotate-left')->setSubItems([
                MenuItem::linkToUrl('retour au site', '', $this->generateUrl('app_home')),
            ]);

            // tricks
            yield MenuItem::section('Tricks');
            yield MenuItem::subMenu('', 'fa-solid fa-person-snowboarding')->setSubItems([
                MenuItem::linkToCrud('ajout d\'un trick', 'fas fa-plus', Trick::class)->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud('Voir les Tricks', 'fas fa-eye', Trick::class)
            ]);
            // account
            yield MenuItem::section(('Mon compte'));
            yield MenuItem::subMenu('Gestion de mon compte', 'fas fa-user')->setSubItems([
                MenuItem::linkToCrud('mon compte', '', User::class)
            ]);
        }
        if ($this->isGranted('ROLE_ADMIN')) {

            // back to home &
            yield MenuItem::section('Navigation');
            yield MenuItem::subMenu('', 'fa-solid fa-arrow-rotate-left')->setSubItems([
                MenuItem::linkToUrl('retour au site', '', $this->generateUrl('app_home')),
            ]);
            // comments
            yield MenuItem::section('Commentaires');
            yield MenuItem::subMenu("Commentaires", "fas fa-comments")->setSubItems([
                MenuItem::linkToCrud('Voir tout', '', Comment::class)->setAction(Crud::PAGE_INDEX)
            ]);
            // users
            yield MenuItem::section(('Gestion des Utilisateurs'));
            yield MenuItem::subMenu('Tous les comptes', 'fa-solid fa-user-group')->setSubItems([
                MenuItem::linkToCrud('Voir les utilisateurs', '', User::class)
            ]);
            // categories
            yield MenuItem::section('Categories');
            yield MenuItem::subMenu('Categories', 'fa-solid fa-tags')->setSubItems([
                MenuItem::linkToCrud('ajout d\'une catégorie', 'fas fa-plus', Category::class),
                MenuItem::linkToCrud('Voir les catégories', 'fas fa-eye', Category::class),
            ]);
            // tricks
            yield MenuItem::section('Tricks');
            yield MenuItem::subMenu('', 'fa-solid fa-person-snowboarding')->setSubItems([
                MenuItem::linkToCrud('ajout d\'un trick', 'fas fa-plus', Trick::class)->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud('Voir les Tricks', 'fas fa-eye', Trick::class)
            ]);
        }
    }

    /**
     * @param UserInterface|User $user
     */
    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setAvatarUrl("/uploads/avatar/" . $user->getAvatar());
    }

    /**
     * Default page all Users
     *
     * @return Actions
     */
    public function configureActions(): Actions
    {
        return parent::configureActions()
        ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
