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
            ->setTitle('Snowtricks');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Navigation');
        yield MenuItem::subMenu('', 'fas fa-bars')->setSubItems([
            MenuItem::linkToDashboard('panneau admin', 'fa-solid fa-dashboard'),
            MenuItem::linkToUrl('retour au site', 'fa-solid fa-house', $this->generateUrl('app_home')),
        ]);

        yield MenuItem::section('Categories');
        yield MenuItem::subMenu('', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('ajout d\'une catégorie', 'fas fa-plus', Category::class),
            MenuItem::linkToCrud('Voir les catégories', 'fas fa-eye', Category::class),
        ]);

        yield MenuItem::section('Tricks');

        yield MenuItem::subMenu('', 'fas fa-bars')->setSubItems([
            MenuItem::linkToCrud('ajout d\'un trick', 'fas fa-plus', Trick::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Voir les Tricks', 'fas fa-eye', Trick::class)
        ]);

        if ($this->isGranted('ROLE_USER')) {
            yield MenuItem::section(('Mon compte'));
            yield MenuItem::subMenu('Gestion de mon compte', 'fas fa-bars')->setSubItems([
                MenuItem::linkToCrud('accéder', 'fas fa-user-friends', User::class)
            ]);
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::section('Commentaires');
            yield MenuItem::subMenu("Commentaires", "fas fa-bars")->setSubItems([
                MenuItem::linkToCrud('Voir tout', "fas fa-comments", Comment::class)->setAction(Crud::PAGE_NEW)
            ]);
            yield MenuItem::section(('Gestion des Utilisateurs'));
            yield MenuItem::subMenu('Tous les comptes', 'fas fa-bars')->setSubItems([
                MenuItem::linkToCrud('Voir les utilisateurs', 'fas fa-user-friends', User::class)
            ]);
        }
    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
        ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}