<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @method Trick 
 */
class UserEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    )
    {
        
    }
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'setHashedPasswordOnCreate',
            BeforeEntityUpdatedEvent::class => 'setHashedPasswordOnUpdate',
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
}
