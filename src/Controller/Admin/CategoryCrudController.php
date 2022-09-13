<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
        ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
            return $action->setIcon('fa-solid fa-folder-open')
            ->setLabel("nouvelle catégorie")
            ;
        })
        ->update(Crud::PAGE_INDEX, Action::DETAIL, function(Action $action){
            return $action->setIcon('fa-solid fa-eye');
        })
        ->update(Crud::PAGE_INDEX, Action::DELETE, function(Action $action){
            return $action->setIcon('fa-solid fa-trash');
        })
        ->update(Crud::PAGE_INDEX, Action::EDIT, function(Action $action){
            return $action->setIcon('fa-solid fa-gears');
        });
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add(TextFilter::new('name', 'nom'));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Nom de la Catégorie'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
        ->setEntityLabelInPlural('Catégories')
        ->setEntityLabelInSingular('Catégorie')
        ->setDefaultSort(['name' => 'asc'])
        ;
    }
}
