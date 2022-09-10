<?php

namespace App\Controller\Admin;

use App\Entity\Trick;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\String\Slugger\SluggerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class TrickCrudController extends AbstractCrudController
{
    public const TRICK_BASE_PATH = 'uploads/tricks/';
    public const TRICK_UPLOAD_DIR = 'public/uploads/tricks/';

    public function __construct(private SluggerInterface $slugger)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Trick::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
        ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setIcon('fa-fw fas fa-person-snowboarding')->setLabel("Nouveau trick");
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


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('category')
            ->setLabel('Catégorie'),
            TextField::new('name', 'Nom du trick'),
            TextareaField::new('Description'),

            ImageField::new('image')
            ->setBasePath(self::TRICK_BASE_PATH)
            ->setUploadDir(self::TRICK_UPLOAD_DIR),

            SlugField::new('slug')->setTargetFieldName("name")
            ->hideOnForm(),
            DateTimeField::new('updatedAt')->onlyOnIndex()->setLabel('mis à jour le'),
            DateTimeField::new('CreatedAt')->onlyOnIndex()->setLabel('date de création'),
        ];
    }
}
