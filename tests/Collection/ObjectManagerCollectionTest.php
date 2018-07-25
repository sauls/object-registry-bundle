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

namespace Sauls\Bundle\ObjectRegistryBundle\Collection;

use PHPUnit\Framework\TestCase;
use Sauls\Bundle\ObjectRegistryBundle\Manager\ConcreteManagerInterface;
use Sauls\Bundle\ObjectRegistryBundle\Manager\ManagerInterface;
use Sauls\Bundle\ObjectRegistryBundle\Manager\NamedManagerInterface;
use Sauls\Component\Collection\ArrayCollection;

class ObjectManagerCollectionTest extends TestCase
{
    public function testShouldNotContainAnyManagers(): void
    {
        $collection = $this->createObjectManagerCollection();

        $this->assertEquals(0, $collection->count());
    }

    /**
     * @param array $managers
     * @return ObjectManagerCollectionInterface|ArrayCollection
     */
    public function createObjectManagerCollection(array $managers = []): ObjectManagerCollectionInterface
    {
        return new ObjectManagerCollection($managers);
    }

    public function testShouldCountAllGroupManagers(): void
    {
        $concreteManager = $this->prophesize(ConcreteManagerInterface::class);
        $concreteManager->getObjectClass()->willReturn(\stdClass::class);

        $namedManager = $this->prophesize(NamedManagerInterface::class);
        $namedManager->getName()->willReturn('test_manager');

        $looseManager = $this->prophesize(ManagerInterface::class);


        $collection = $this->createObjectManagerCollection([
            $concreteManager->reveal(),
            $namedManager->reveal(),
            $looseManager->reveal()
        ]);

        $this->assertEquals(3, $collection->count());
    }

    public function testShouldClearAllManagers(): void
    {
        $concreteManager = $this->prophesize(ConcreteManagerInterface::class);
        $concreteManager->getObjectClass()->willReturn(\stdClass::class);

        $namedManager = $this->prophesize(NamedManagerInterface::class);
        $namedManager->getName()->willReturn('test_manager');

        $looseManager = $this->prophesize(ManagerInterface::class);


        $collection = $this->createObjectManagerCollection([
            $concreteManager->reveal(),
            $namedManager->reveal(),
            $looseManager->reveal()
        ]);

        $this->assertEquals(3, $collection->count());

        $collection->clear();

        $this->assertEquals(0, $collection->count());
    }
}
