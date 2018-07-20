<?php
/**
 * This file is part of the sauls/object-registry-bundle package.
 *
 * @author    Saulius Vaičeliūnas <vaiceliunas@inbox.lt>
 * @link      http://saulius.vaiceliunas.lt
 * @copyright 2018
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sauls\Bundle\ObjectRegistryBundle\EvenListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Sauls\Bundle\ObjectRegistryBundle\Event\DoctrineObjectEvents;
use Sauls\Bundle\ObjectRegistryBundle\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DoctrineEventsSubscriber implements EventSubscriberInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::prePersist => ['onPrePersist'],
            Events::postPersist => ['onPostPersist'],
            Events::preFlush => ['onPreFlush'],
            Events::postFlush => ['onPostFlush'],
            Events::preUpdate => ['onPreUpdate'],
            Events::postUpdate => ['onPostUpdate'],
            Events::preRemove => ['onPreRemove'],
            Events::postRemove => ['onPostRemove'],
        ];
    }

    public function onPrePersist(LifecycleEventArgs $args): void
    {
        $eventName = $this->eventDispatcher->createEventNameForObject(DoctrineObjectEvents::PRE_SAVE, $args->getObject());
    }
}
