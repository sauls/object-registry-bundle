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

namespace Sauls\Bundle\ObjectRegistryBundle\Event;

use Doctrine\Common\EventArgs;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs as ORMLifecycleEventArgs;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\TestCase;

class GenericDoctrineObjectEventTest extends TestCase
{
    private $entityManager;

    public function testShouldGetEntity(): void
    {
        $object = $this->prophesize(\stdClass::class);
        $parent = $this->prophesize(EventArgs::class);

        $event = new GenericDoctrineObjectEvent(
            $object->reveal(),
            $parent->reveal()
        );

        $this->assertEquals($object->reveal(), $event->getEntity());
    }

    public function testShouldGetParent(): void
    {
        $object = $this->prophesize(\stdClass::class);
        $parent = $this->prophesize(EventArgs::class);

        $event = new GenericDoctrineObjectEvent(
            $object->reveal(),
            $parent->reveal()
        );

        $this->assertEquals($parent->reveal(), $event->getParent());
    }

    public function testShouldSetParent(): void
    {
        $object = $this->prophesize(\stdClass::class);
        $parent = $this->prophesize(EventArgs::class);
        $differentParent = $this->prophesize(LifecycleEventArgs::class);

        $event = new GenericDoctrineObjectEvent(
            $object->reveal(),
            $parent->reveal()
        );

        $event->setParent($differentParent->reveal());
        $this->assertEquals($differentParent->reveal(), $event->getParent());
    }

    public function testShouldGetChangeSet(): void
    {
        $object = $this->prophesize(\stdClass::class);
        $parent = $this->prophesize(LifecycleEventArgs::class);

        $uowMock = $this->getMockBuilder(UnitOfWork::class)->disableOriginalConstructor()->getMock();
        $uowMock->method('getEntityChangeSet')->willReturn(['testprop' => []]);

        $this->entityManager->getUnitOfWork()->willReturn($uowMock);
        $parent->getObjectManager()->willReturn($this->entityManager->reveal());

        $event = new GenericDoctrineObjectEvent(
            $object->reveal(),
            $parent->reveal()
        );

        $changeSet = $event->getChangeSet();

        $this->assertEquals(['testprop' => []], $changeSet);
    }

    public function testShouldGetChangeSetByPropertyName(): void
    {
        $object = $this->prophesize(\stdClass::class);
        $parent = $this->prophesize(ORMLifecycleEventArgs::class);

        $uowMock = $this->getMockBuilder(UnitOfWork::class)->disableOriginalConstructor()->getMock();
        $uowMock->method('getEntityChangeSet')->willReturn(['testprop' => ['p' => 'p', 'o' => 'o']]);

        $this->entityManager->getUnitOfWork()->willReturn($uowMock);
        $parent->getEntityManager()->willReturn($this->entityManager->reveal());

        $event = new GenericDoctrineObjectEvent(
            $object->reveal(),
            $parent->reveal()
        );

        $changeSet = $event->getChangeSet('testprop');

        $this->assertEquals(['p' => 'p', 'o' => 'o'], $changeSet);
    }

    public function testGetChangeSetByPropertyShouldReturnFalse(): void
    {
        $object = $this->prophesize(\stdClass::class);
        $parent = $this->prophesize(LifecycleEventArgs::class);

        $uowMock = $this->getMockBuilder(UnitOfWork::class)->disableOriginalConstructor()->getMock();
        $uowMock->method('getEntityChangeSet')->willReturn(['testprop' => ['p' => 'p', 'o' => 'o']]);

        $this->entityManager->getUnitOfWork()->willReturn($uowMock);
        $parent->getObjectManager()->willReturn($this->entityManager->reveal());

        $event = new GenericDoctrineObjectEvent(
            $object->reveal(),
            $parent->reveal()
        );

        $this->assertFalse($event->getChangeSet('non_existent'));

    }

    public function testShouldNotGetEntityManager(): void
    {
        $this->expectException(\RuntimeException::class);
        $object = $this->prophesize(\stdClass::class);
        $parent = $this->prophesize(EventArgs::class);

        $event = new GenericDoctrineObjectEvent(
            $object->reveal(),
            $parent->reveal()
        );

        $event->getEntityManager();
    }

    protected function setUp()
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
    }


}
