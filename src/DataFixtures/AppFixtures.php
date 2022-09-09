<?php

namespace App\DataFixtures;

use App\Entity\Category;
use DateTimeZone;
use App\Entity\Trick;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use PhpParser\Node\Stmt\Catch_;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $category = new Category();
        $category->setName('Stances');
        $manager->persist($category);

        $trick = new Trick();
        $trick->setCreatedAt(
            new DateTimeImmutable("now", new DateTimeZone('Europe/Paris'))
        )->setName('Regular')
        ->setDescription("Rider avec le pied gauche devant dans sa position naturelle")
        ->setImage("/public/uploads/regular.jpg");
        $manager->persist($trick);
        
        $manager->flush();
    }
}
