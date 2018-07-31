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
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Sauls\Bundle\ObjectRegistryBundle\Event\GenericDoctrineObjectEvent;
use Sauls\Bundle\ObjectRegistryBundle\EventDispatcher\EventDispatcherInterface;
use Sauls\Bundle\ObjectRegistryBundle\Factory\EventNameFactoryInterface;

class DoctrineEventsSubscriberTest extends TestCase
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var EventNameFactoryInterface
     */
    private $eventNameFactory;

    public function testShouldReturnSubscribedEventsArray(): void
    {
        $eventSubscriber = $this->createDoctrineEventSubscriber();
        $this->assertEquals([
            'prePersist' => 'prePersist',
            'postPersist' => 'postPersist',
            'preUpdate' => 'preUpdate',
            'postUpdate' => 'postUpdate',
            'preRemove' => 'preRemove',
            'postRemove' => 'postRemove',
        ], $eventSubscriber->getSubscribedEvents());
    }

    public function testShouldDispatchPrePersistEvent(): void
    {
        $eventSubscriber = $this->createDoctrineEventSubscriber();
        $event = $this->prophesize(LifecycleEventArgs::class);
        $entity = $this->prophesize(\stdClass::class);
        $event->getEntity()->willReturn($entity->reveal());
        $this->eventNameFactory
            ->create('sauls.object_registry.event.pre_doctrine_object_persist', $entity)
            ->willReturn('sauls.object_registry.event.pre_doctrine_object_persist.std_class');


        $this->eventDispatcher->dispatch(
            'sauls.object_registry.event.pre_doctrine_object_persist.std_class',
            Argument::type(GenericDoctrineObjectEvent::class)
        )->shouldBeCalled();

        $eventSubscriber->prePersist($event->reveal());
    }

    public function createDoctrineEventSubscriber(): DoctrineEventsSubscriber
    {
        return new DoctrineEventsSubscriber(
            $this->eventDispatcher->reveal(),
            $this->eventNameFactory->reveal()
        );
    }

    public function testShouldDispatchPostPersistEvent(): void
    {
        $eventSubscriber = $this->createDoctrineEventSubscriber();
        $event = $this->prophesize(LifecycleEventArgs::class);
        $entity = $this->prophesize(\stdClass::class);
        $event->getEntity()->willReturn($entity->reveal());
        $this->eventNameFactory
            ->create('sauls.object_registry.event.post_doctrine_object_persist', $entity)
            ->willReturn('sauls.object_registry.event.post_doctrine_object_persist.std_class');


        $this->eventDispatcher->dispatch(
            'sauls.object_registry.event.post_doctrine_object_persist.std_class',
            Argument::type(GenericDoctrineObjectEvent::class)
        )->shouldBeCalled();

        $eventSubscriber->postPersist($event->reveal());
    }

    public function testShouldDispatchPreUpdateEvent(): void
    {
        $eventSubscriber = $this->createDoctrineEventSubscriber();
        $event = $this->prophesize(PreUpdateEventArgs::class);
        $entity = $this->prophesize(\stdClass::class);
        $event->getEntity()->willReturn($entity->reveal());
        $this->eventNameFactory
            ->create('sauls.object_registry.event.pre_doctrine_object_update', $entity)
            ->willReturn('sauls.object_registry.event.pre_doctrine_object_update.std_class');


        $this->eventDispatcher->dispatch(
            'sauls.object_registry.event.pre_doctrine_object_update.std_class',
            Argument::type(GenericDoctrineObjectEvent::class)
        )->shouldBeCalled();

        $eventSubscriber->preUpdate($event->reveal());
    }

    public function testShouldDispatchPostUpdateEvent(): void
    {
        $eventSubscriber = $this->createDoctrineEventSubscriber();
        $event = $this->prophesize(LifecycleEventArgs::class);
        $entity = $this->prophesize(\stdClass::class);
        $event->getEntity()->willReturn($entity->reveal());
        $this->eventNameFactory
            ->create('sauls.object_registry.event.post_doctrine_object_update', $entity)
            ->willReturn('sauls.object_registry.event.post_doctrine_object_update.std_class');


        $this->eventDispatcher->dispatch(
            'sauls.object_registry.event.post_doctrine_object_update.std_class',
            Argument::type(GenericDoctrineObjectEvent::class)
        )->shouldBeCalled();

        $eventSubscriber->postUpdate($event->reveal());
    }

    public function testShouldDispatchPreRemoveEvent(): void
    {
        $eventSubscriber = $this->createDoctrineEventSubscriber();
        $event = $this->prophesize(LifecycleEventArgs::class);
        $entity = $this->prophesize(\stdClass::class);
        $event->getEntity()->willReturn($entity->reveal());
        $this->eventNameFactory
            ->create('sauls.object_registry.event.pre_doctrine_object_remove', $entity)
            ->willReturn('sauls.object_registry.event.pre_doctrine_object_remove.std_class');


        $this->eventDispatcher->dispatch(
            'sauls.object_registry.event.pre_doctrine_object_remove.std_class',
            Argument::type(GenericDoctrineObjectEvent::class)
        )->shouldBeCalled();

        $eventSubscriber->preRemove($event->reveal());
    }

    public function testShouldDispatchPostRemoveEvent(): void
    {
        $eventSubscriber = $this->createDoctrineEventSubscriber();
        $event = $this->prophesize(LifecycleEventArgs::class);
        $entity = $this->prophesize(\stdClass::class);
        $event->getEntity()->willReturn($entity->reveal());
        $this->eventNameFactory
            ->create('sauls.object_registry.event.post_doctrine_object_remove', $entity)
            ->willReturn('sauls.object_registry.event.post_doctrine_object_remove.std_class');


        $this->eventDispatcher->dispatch(
            'sauls.object_registry.event.post_doctrine_object_remove.std_class',
            Argument::type(GenericDoctrineObjectEvent::class)
        )->shouldBeCalled();

        $eventSubscriber->postRemove($event->reveal());
    }

    protected function setUp()
    {
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->eventNameFactory = $this->prophesize(EventNameFactoryInterface::class);
    }


}
