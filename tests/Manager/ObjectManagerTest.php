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

namespace Sauls\Bundle\ObjectRegistryBundle\Manager;

use PHPUnit\Framework\TestCase;
use Sauls\Bundle\ObjectRegistryBundle\Event\GenericObjectManagerEvent;
use Sauls\Bundle\ObjectRegistryBundle\Event\ObjectEvents;
use Sauls\Bundle\ObjectRegistryBundle\Stubs\SampleObject;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ObjectManagerTest extends TestCase
{
    private $eventDispatcher;

    public function testShouldCreateObjectInstanceOfGivenClass(): void
    {
        $objectManager = $this->createObjectManager(SampleObject::class);

        $object = $objectManager->create([]);

        $this->assertInstanceOf(SampleObject::class, $object);
    }

    /**
     * @return ObjectManager
     */
    protected function createObjectManager(string $class): ObjectManager
    {
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        return new ObjectManager($class, $this->eventDispatcher->reveal());
    }

    public function testShouldModifyObjectInstanceWithGivenProperties(): void
    {
        $objectManager = $this->createObjectManager(SampleObject::class);
        $sampleObject = $objectManager->create();

        $sampleObject = $objectManager->modify($sampleObject, [
            'property1' => 'hello world',
            'property2' => 'testing the world',
        ]);

        $this->assertEquals('hello world', $sampleObject->property1);
        $this->assertEquals('testing the world', $sampleObject->getProperty2());
    }
}
