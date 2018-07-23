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
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
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

    public function onPrePersist(LifecycleEventArgs $args)
    {
    }

    public function onPostPersist(LifecycleEventArgs $args)
    {
    }

    public function onPreUpdate(PreUpdateEventArgs $args)
    {
    }

    public function onPostUpdate(LifecycleEventArgs $args)
    {
    }

    public function onPreRemove(LifecycleEventArgs $args)
    {
    }

    public function onPostRemove(LifecycleEventArgs $args)
    {
    }
}
