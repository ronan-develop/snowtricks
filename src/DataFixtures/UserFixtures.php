<?php

namespace App\DataFixtures;

use DateTimeZone;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $password = "password";

        $admin = new User();
        $admin->setUsername('admin')
        ->setEmail('admin@mail.fr')
        ->setPassword($this->hasher->hashPassword($admin, $password))
        ->setCreatedAt(new DateTimeImmutable("now", new DateTimeZone('Europe/Paris')))
        ->setSlug('bob')
        ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $user1 = new User();
        $user1->setUsername('Bob')
        ->setEmail('bob@mail.fr')
        ->setPassword($this->hasher->hashPassword($user1, $password))
        ->setCreatedAt(new DateTimeImmutable("now", new DateTimeZone('Europe/Paris')))
        ->setSlug('bob')
        ->setRoles(['ROLE_USER']);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername('Bill')
        ->setEmail('bill@mail.fr')
        ->setPassword($this->hasher->hashPassword($user2, $password))
        ->setCreatedAt(new DateTimeImmutable("now", new DateTimeZone('Europe/Paris')))
        ->setSlug('Bill')
        ->setRoles(['ROLE_USER']);
        ;
        $manager->persist($user2);



        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['user'];
    }
}
