<?php

namespace App\Controller\Admin;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\String\Slugger\SluggerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * @method User getUser
 */
class TrickCrudController extends AbstractCrudController
{
    public const TRICK_BASE_PATH = 'uploads/tricks/';
    public const TRICK_UPLOAD_DIR = 'public/uploads/tricks/';

    public function __construct(private SluggerInterface $slugger, private TrickRepository $trickRepository)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Trick::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser()->getId() !== $this->trickRepository->findBy(['id' => $this->getUser()->getId()])) {
            return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ;
        }
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
        yield IdField::new('id')->hideOnForm()
        ->hideOnIndex();
        yield AssociationField::new('category')
        ->setLabel('Catégorie');
        yield TextField::new('name', 'Nom du trick');
        yield TextareaField::new('Description');

        yield SlugField::new('slug')->setTargetFieldName("name")
        ->hideOnForm();
        yield DateTimeField::new('updatedAt')->onlyOnIndex()->setLabel('mis à jour le');
        yield DateTimeField::new('CreatedAt')->onlyOnIndex()->setLabel('date de création');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        ->setEntityLabelInPlural('Figures')
        ->setEntityLabelInSingular('Figure')
        ->setDefaultSort(['name' => 'desc'])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
        ->add(EntityFilter::new('user', 'Utilisateur'))
        ->add(TextFilter::new('name', 'Nom'))
        ->add(EntityFilter::new('category', 'Catégorie'));
    }
}
