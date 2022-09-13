<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @method User getUser
 */
class UserCrudController extends AbstractCrudController
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
        private EntityRepository $entityRepository
        )
    {
    }
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser()->getId() !== $this->userRepository->findBy(['id' => $this->getUser()->getId()])) {
            $userId = $this->getUser()->getId();

            $queryBuilder = $this->entityRepository->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
            $queryBuilder->andWHere('entity.id = :userId')->setParameter('userId', $userId);

            return $queryBuilder;
        }

        return $this->entityRepository->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
    }

    public function configureActions(Actions $actions): Actions
    {
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser()->getId() !== $this->userRepository->findBy(['id' => $this->getUser()->getId()])) {
            return $actions
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

    public function configureFields(string $pagename): iterable
    {
        yield TextField::new('username');
        yield TextField::new('password')
        ->setFormType(PasswordType::class)
        ->onlyOnForms();
        yield TextField::new('email')
        ->setFormType(EmailType::class);

        if ($this->isGranted('ROLE_ADMIN')) {
            yield ChoiceField::new('roles')
            ->allowMultipleChoices()
            ->renderAsBadges([
                'ROLE_ADMIN'=>'success',
                'ROLE_USER'=>'warning'
            ])
            ->setChoices([
                'Administrateur' => 'ROLE_ADMIN',
                'Utilisateur' => 'ROLE_USER'
            ]);
        }
    }

    public function persistEntity(EntityManagerInterface $em, $entityInstance): void
    {
        $user = $entityInstance;

        $plainPassword = $user->getPassword();

        $hashedPassword = $this->userPasswordHasher->hashPassword($user, $plainPassword);

        $user->setPassword($hashedPassword);

        parent::persistEntity($em, $entityInstance);
    }
    
}


// if (!$this->isGranted('ROLE_ADMIN') && $this->getUser()->getId() !== $this->userRepository->findBy(['id' => $this->getUser()->getId()])) {
        //     return $actions
        //     ->remove(Crud::PAGE_INDEX, Action::DELETE)
        //     ->remove(Crud::PAGE_INDEX, Action::EDIT)
        //     ->remove(Crud::PAGE_DETAIL, Action::DELETE)
        //     ->remove(Crud::PAGE_DETAIL, Action::EDIT)
        //     ;
        // }