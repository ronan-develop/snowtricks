<?php

namespace App\EventSubscriber;

use DateTimeZone;
use App\Entity\User;
use DateTimeImmutable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @method Trick
 * @method User
 */
class UserEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SluggerInterface $slugger,
        private UserPasswordHasherInterface $hasher
    )
    {
        
    }
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => [
                ['setHashedPasswordOnCreate'],
                ['setCreatedAtBeforePersist'],
                ['setSlugBeforePersist'],
                ['setEmailBeforePersist'],
            ],
            BeforeEntityUpdatedEvent::class => [
                ['setHashedPasswordOnUpdate'],
                ['setUpdatedAtBeforePersist'],
                ['setSlugBeforeUpdate']
            ]
        ];
    }

    public function setHashedPasswordOnCreate(BeforeEntityPersistedEvent $event)
    {
        $user = $event->getEntityInstance();
        if (!$user instanceof User) {
            return;
        }
        $user->setPassword(
            $this->hasher->hashPassword($user, $user->getPassword())
        );

    }

    public function setCreatedAtBeforePersist(BeforeEntityPersistedEvent $event)
    {
        $user = $event->getEntityInstance();
        if (!$user instanceof User) {
            return;
        }
        $user->setCreatedAt(new DateTimeImmutable("now", new DateTimeZone('Europe/Paris')));
    }
    
    public function setHashedPasswordOnUpdate(BeforeEntityUpdatedEvent $event)
    {
        $user = $event->getEntityInstance();
        
        if (!$user instanceof User) {
            return;
        }

        $user->setPassword(
            $this->hasher->hashPassword($user, $user->getPassword())
        );
    }

    public function setUpdatedAtBeforePersist(BeforeEntityUpdatedEvent $event)
    {
        $user = $event->getEntityInstance();
        if (!$user instanceof User) {
            return;
        }
        $user->setUpdatedAt(new DateTimeImmutable("now", new DateTimeZone('Europe/Paris')));
    }

    public function setSlugBeforePersist(BeforeEntityPersistedEvent $event)
    {
        $user = $event->getEntityInstance();
        if (!$user instanceof User) {
            return;
        }
        $user->setSlug(
            $this->slugger->slug(strtolower($user->getUsername()))
        );
    }

    public function setSlugBeforeUpdate(BeforeEntityUpdatedEvent $event)
    {
        $user = $event->getEntityInstance();
        if (!$user instanceof User) {
            return;
        }
        $user->setSlug(
            $this->slugger->slug(strtolower($user->getUsername()))
        );
    }
}
