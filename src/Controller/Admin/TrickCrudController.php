<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Trick;
use App\Repository\TrickRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Vich\UploaderBundle\Form\Type\VichImageType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
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
    public function __construct(
        private SluggerInterface $slugger,
        private TrickRepository $trickRepository,
        private  string $uploadDir
    ) {
    }


    public static function getEntityFqcn(): string
    {
        return Trick::class;
    }

    public function redirectToLogin()
    {
        $this->redirectToRoute('app_login');
        return $this;
    }

    public function configureActions(Actions $actions): Actions
    {
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser()->getId() !== $this->trickRepository->findBy(
            ['id' => $this->getUser()->getId()]
        )) {
            return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_DETAIL, Action::EDIT)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE)
            ->remove(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN)
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
        if ($this->getUser()->getId() !== $this->trickRepository->findOneBy(['id'=> $this->getUser()->getId()])) {
            $this->redirectToRoute('app_login');
        }

        yield IdField::new('id')->hideOnForm()
        ->hideOnIndex();

        yield AssociationField::new('category', 'Catégorie(s)');

        yield TextField::new('name', 'Nom du trick');
        yield SlugField::new('slug')->setTargetFieldName("name")
        ->setUnlockConfirmationMessage("Le Slug est généré automatiquement, mais il peut etre modifié");

        yield TextareaField::new('Description');

        yield TextField::new('file', 'Image')
        ->setFormType(VichImageType::class)
        ->onlyOnForms();
        yield ImageField::new('image', 'Image')
        ->setBasePath($this->uploadDir . '/tricks')
        ->setUploadDir('public/tricks/' . $this->uploadDir)
        ->hideOnForm();

        yield DateTimeField::new('updatedAt')->setLabel('mis à jour le')->hideOnForm();
        yield DateTimeField::new('CreatedAt')->setLabel('date de création')->hideOnForm();
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
