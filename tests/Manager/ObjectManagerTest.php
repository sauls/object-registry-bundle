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
use Sauls\Bundle\ObjectRegistryBundle\EventDispatcher\EventDispatcherInterface;
use Sauls\Bundle\ObjectRegistryBundle\Exception\UnsupportedObjectClassException;
use Sauls\Bundle\ObjectRegistryBundle\Stubs\SampleObject;

class ObjectManagerTest extends TestCase
{
    private $eventDispatcher;

    public function testShouldCreateObjectInstanceOfGivenClass(): void
    {
        $objectManager = $this->createObjectManager(SampleObject::class);

        $object = $objectManager->create([]);

        $this->assertInstanceOf(SampleObject::class, $object);
    }

    protected function createObjectManager(string $class): ObjectManager
    {
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $objectManager = new ObjectManager($this->eventDispatcher->reveal());
        $objectManager->setObjectClass($class);

        return $objectManager;
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

    public function testShouldThrowUnsupportedObjectClassException(): void
    {
        $this->expectException(UnsupportedObjectClassException::class);
        $this->expectExceptionMessage('Object manager of `Sauls\Bundle\ObjectRegistryBundle\Stubs\SampleObject` object class does not support given `stdClass` class');
        $objectManager = $this->createObjectManager(SampleObject::class);
        $pretendedObject = new \stdClass();

        $objectManager->modify($pretendedObject, ['value' => 'new value']);
    }

    public function testObjectManagerShouldReturnItsDefaultName(): void
    {
        $objectManager = $this->createObjectManager(SampleObject::class);
        $this->assertEquals('default.object_manager', $objectManager->getName());
    }

    public function testShouldReturnFalseWhenCheckingSupportForUnsupportedObject(): void
    {
        $objectManager = $this->createObjectManager(SampleObject::class);
        $this->assertFalse($objectManager->supports(new \stdClass));
    }

    public function testShouldReturnFalseWhenCheckingSupportForUnsupportedClass(): void
    {
        $objectManager = $this->createObjectManager(SampleObject::class);
        $this->assertFalse($objectManager->supports(\stdClass::class));
    }

    public function testShouldReturnFalseWhenCheckingOnNotSupportedValueTypes(): void
    {
        $objectManager = $this->createObjectManager(SampleObject::class);
        $this->assertFalse($objectManager->supports([]));
        $this->assertFalse($objectManager->supports(null));
        $this->assertFalse($objectManager->supports(0));
        $this->assertFalse($objectManager->supports(1));
        $this->assertFalse($objectManager->supports(199));
        $this->assertFalse($objectManager->supports(19.9));
    }
}
