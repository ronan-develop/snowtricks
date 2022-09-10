<?php

namespace App\DataFixtures;

use App\Entity\Category;
use DateTimeZone;
use App\Entity\Trick;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

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
        ->setSlug("stances")
        ->setDescription("Rider avec le pied gauche devant dans sa position naturelle")
        ->setImage("regular.jpg");
        $manager->persist($trick);

        $manager->flush();
    }
}
