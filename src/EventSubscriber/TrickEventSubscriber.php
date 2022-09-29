<?php

namespace App\EventSubscriber;

use DateTimeZone;
use App\Entity\Trick;
use DateTimeImmutable;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;

/**
 * @method Trick 
 */
class TrickEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => [
                'setTrickCreatedAt',
            ],
            BeforeEntityUpdatedEvent::class => [
                'setTrickUpdatedAt'
            ]
        ];
    }

    public function setTrickCreatedAt(BeforeEntityPersistedEvent $event)
    {
        $trick = $event->getEntityInstance();

        if (!$trick instanceof Trick) {
            return;
        }

        $trick->setCreatedAt(new DateTimeImmutable("now", new DateTimeZone('Europe/Paris')));
    }

    public function checkTrick(BeforeEntityPersistedEvent $event)
    {
        $trick = $event->getEntityInstance();

        if (!$trick instanceof Trick) {
            return;
        }

        if ($trick->getImage() == null) {
            $trick->setImage('/uploads/tricks/300x200.gif');
        }
        
    }

    public function setTrickUpdatedAt(BeforeEntityUpdatedEvent $event)
    {
        $trick = $event->getEntityInstance();

        if (!$trick instanceof Trick) {
            return;
        }

        $trick->setUpdatedAt(new DateTimeImmutable("now", new DateTimeZone('Europe/Paris')));
    }
}
