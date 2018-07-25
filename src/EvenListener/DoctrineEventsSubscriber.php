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

use Doctrine\Common\EventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Sauls\Bundle\ObjectRegistryBundle\Event\DoctrineObjectEvents;
use Sauls\Bundle\ObjectRegistryBundle\Event\GenericDoctrineObjectEvent;
use Sauls\Bundle\ObjectRegistryBundle\EventDispatcher\EventDispatcherInterface;
use Sauls\Bundle\ObjectRegistryBundle\Factory\EventNameFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DoctrineEventsSubscriber implements EventSubscriberInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var EventNameFactory
     */
    private $eventNameFactory;

    public function __construct(EventDispatcherInterface $eventDispatcher, EventNameFactory $eventNameFactory)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->eventNameFactory = $eventNameFactory;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::prePersist => ['onPrePersist'],
            Events::postPersist => ['onPostPersist'],
            Events::preUpdate => ['onPreUpdate'],
            Events::postUpdate => ['onPostUpdate'],
            Events::preRemove => ['onPreRemove'],
            Events::postRemove => ['onPostRemove'],
        ];
    }

    public function onPrePersist(LifecycleEventArgs $event): void
    {
        $this->process(DoctrineObjectEvents::PRE_PERSIST, $event);
    }

    public function onPostPersist(LifecycleEventArgs $event): void
    {
        $this->process(DoctrineObjectEvents::POST_PERSIST, $event);
    }

    public function onPreUpdate(PreUpdateEventArgs $event): void
    {
        $this->process(DoctrineObjectEvents::PRE_UPDATE, $event);
    }

    public function onPostUpdate(LifecycleEventArgs $event): void
    {
        $this->process(DoctrineObjectEvents::POST_UPDATE, $event);
    }

    public function onPreRemove(LifecycleEventArgs $event): void
    {
        $this->process(DoctrineObjectEvents::PRE_REMOVE, $event);
    }

    public function onPostRemove(LifecycleEventArgs $event): void
    {
        $this->process(DoctrineObjectEvents::POST_REMOVE, $event);
    }

    /**
     * @param string $eventName
     * @param EventArgs|LifecycleEventArgs|PreUpdateEventArgs $event
     */
    private function process(string $eventName, EventArgs $event): void
    {
        if (!$this->hasMethod($event, 'getEntity')) {
            return;
        }

        $entity = $event->getEntity();
        $eventName = $this->eventNameFactory->createEventNameForObject($eventName, $entity);
        $newEvent = new GenericDoctrineObjectEvent($entity, $event);

        $this->eventDispatcher->dispatch($eventName, $newEvent);
    }

    private function hasMethod(object $object, string $method): bool
    {
        return \method_exists($object, $method);
    }
}
