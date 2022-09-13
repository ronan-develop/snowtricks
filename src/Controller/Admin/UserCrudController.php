<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * @method User getUser
 */
class UserCrudController extends AbstractCrudController
{
    public function __construct(private UserRepository $userRepository)
    {
    }
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser()->getId() !== $this->userRepository->findBy(['id' => $this->getUser()->getId()])) {
            return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ;
        }

        return $actions
        ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setIcon('fa-fw fa-solid fa-plus')
            ->setLabel("Nouvel utilisateur");
        })
        ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
            return $action
            ->setIcon('fa-solid fa-gears');
        })
        ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
            return $action
            ->setIcon('fa-solid fa-trash');
        })
        ->update(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER, function (Action $action) {
            return $action
            ->setLabel("Sauvegarder puis ajouter un nouveau");
        })
        ->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN, function (Action $action) {
            return $action
            ->setLabel('Créer et quitter');
        })
        ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, function (Action $action) {
            return $action
            ->setLabel("Sauvegarder puis quitter")
            ->setIcon("fa-solid fa-floppy-disk");
        })
        ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $action) {
            return $action->setIcon('fa-solid fa-eye');
        });
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission('ROLE_USER');
    }

    
}
