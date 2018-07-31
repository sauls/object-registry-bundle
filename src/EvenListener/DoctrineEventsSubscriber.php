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

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Sauls\Bundle\ObjectRegistryBundle\Event\DoctrineObjectEvents;
use Sauls\Bundle\ObjectRegistryBundle\Event\GenericDoctrineObjectEvent;
use Sauls\Bundle\ObjectRegistryBundle\EventDispatcher\EventDispatcherInterface;
use Sauls\Bundle\ObjectRegistryBundle\Factory\EventNameFactoryInterface;

class DoctrineEventsSubscriber implements EventSubscriber
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var EventNameFactoryInterface
     */
    private $eventNameFactory;

    public function __construct(EventDispatcherInterface $eventDispatcher, EventNameFactoryInterface $eventNameFactory)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->eventNameFactory = $eventNameFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist => 'prePersist',
            Events::postPersist => 'postPersist',
            Events::preUpdate => 'preUpdate',
            Events::postUpdate => 'postUpdate',
            Events::preRemove => 'preRemove',
            Events::postRemove => 'postRemove',
        ];
    }

    public function prePersist(LifecycleEventArgs $event): void
    {
        $this->process(DoctrineObjectEvents::PRE_PERSIST, $event);
    }

    /**
     * @param string                                $eventName
     * @param LifecycleEventArgs|PreUpdateEventArgs $event
     */
    private function process(string $eventName, LifecycleEventArgs $event): void
    {
        $entity = $event->getEntity();
        $eventName = $this->eventNameFactory->create($eventName, $entity);
        $newEvent = new GenericDoctrineObjectEvent($entity, $event);

        $this->eventDispatcher->dispatch($eventName, $newEvent);
    }

    public function postPersist(LifecycleEventArgs $event): void
    {
        $this->process(DoctrineObjectEvents::POST_PERSIST, $event);
    }

    public function preUpdate(PreUpdateEventArgs $event): void
    {
        $this->process(DoctrineObjectEvents::PRE_UPDATE, $event);
    }

    public function postUpdate(LifecycleEventArgs $event): void
    {
        $this->process(DoctrineObjectEvents::POST_UPDATE, $event);
    }

    public function preRemove(LifecycleEventArgs $event): void
    {
        $this->process(DoctrineObjectEvents::PRE_REMOVE, $event);
    }

    public function postRemove(LifecycleEventArgs $event): void
    {
        $this->process(DoctrineObjectEvents::POST_REMOVE, $event);
    }
}
