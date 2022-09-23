<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Trick;
use App\Entity\Category;
use App\Controller\Admin\TrickCrudController;
use App\Entity\Comment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator,
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
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
         * Admin is user too -> remove link to 'Gestion de mon compte'
         */
        if( $this->isGranted('ROLE_USER') && !$this->isGranted('ROLE_ADMIN') ) {
            // back to home
            yield MenuItem::section('Navigation');
            yield MenuItem::subMenu('', 'fa-solid fa-arrow-rotate-left')->setSubItems([
                MenuItem::linkToUrl('retour au site', '', $this->generateUrl('app_home')),
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
                MenuItem::linkToCrud('Voir tout', '', Comment::class)->setAction(Crud::PAGE_NEW)
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
     * Default page all Uzers
     *
     * @return Actions
     */
    public function configureActions(): Actions
    {
        return parent::configureActions()
        ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
