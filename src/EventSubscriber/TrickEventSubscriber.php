<?php

namespace App\EventSubscriber;

use DateTimeZone;
use App\Entity\Trick;
use DateTimeImmutable;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;

/**
 * @method Trick
 */
class TrickEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private SluggerInterface $slugger)
    {}
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => [
                'setTrickCreatedAt'
            ],
            BeforeEntityUpdatedEvent::class => [
                'setTrickUpdatedAt'
            ],
            BeforeEntityUpdatedEvent::class => [
                'setSlug'
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

    function setSlug(BeforeEntityUpdatedEvent $event)
    {
        $trick = $event->getEntityInstance();

        if (!$trick instanceof Trick) {
            return;
        }

        $slug = $this->slugger->slug($trick->getName());

        $trick->setSlug(strtolower($slug));
    }
}
