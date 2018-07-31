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

namespace Sauls\Bundle\ObjectRegistryBundle\Factory;

use PHPUnit\Framework\TestCase;
use Sauls\Bundle\ObjectRegistryBundle\Event\GenericDoctrineObjectEvent;
use Sauls\Bundle\ObjectRegistryBundle\Exception\CannotCreateEventNameForCollectionException;
use Sauls\Bundle\ObjectRegistryBundle\Stubs\ObjectWithGetCollectionMethod;
use Sauls\Bundle\ObjectRegistryBundle\Stubs\ObjectWithGetObjectClassMethod;
use Sauls\Bundle\ObjectRegistryBundle\Stubs\ObjectWithGetObjectMethod;
use Sauls\Bundle\ObjectRegistryBundle\Stubs\SampleObject;
use Sauls\Component\Collection\ArrayCollection;
use Symfony\Component\EventDispatcher\GenericEvent;

class EventNameFactoryTest extends TestCase
{
    public function createEventNameFactory(): EventNameFactoryInterface
    {
        return new EventNameFactory;
    }

    public function testShouldCreateEventNameForClass(): void
    {
        $factory = $this->createEventNameFactory();
        $this->assertEquals(
            'test_event.my_class',
            $factory->createEventNameForClass('test_event', 'Test\MyClass')
        );
    }

    public function testShouldCreateEventNameForObject(): void
    {
        $factory = $this->createEventNameFactory();
        $object = new SampleObject;

        $this->assertEquals(
            'test_event.sample_object',
            $factory->createEventNameForObject('test_event', $object)
        );
    }

    public function testShouldCreateEventNameForObjectWithCreateMethod(): void
    {
        $factory = $this->createEventNameFactory();
        $object = new SampleObject;

        $this->assertEquals(
            'test_event.sample_object',
            $factory->create('test_event', $object)
        );
    }

    public function testShouldCreateEventNameForCollection(): void
    {
        $factory = $this->createEventNameFactory();
        $collection = ArrayCollection::create([
            new SampleObject,
            new SampleObject,
        ]);

        $this->assertEquals(
            'test_collection_event.sample_object',
            $factory->createEventNameForCollection('test_collection_event', $collection->all())
        );
    }

    public function testShouldThrowExceptionOnEmptyCollection(): void
    {
        $factory = $this->createEventNameFactory();
        $collection = [];

        $this->expectException(CannotCreateEventNameForCollectionException::class);

        $factory->createEventNameForCollection('test_collection_event', $collection);
    }

    public function testShouldCreateEventNameForObjectWithGetSubjectMethod(): void
    {
        $factory = $this->createEventNameFactory();
        $event = $this->prophesize(GenericEvent::class);
        $event->getSubject()->willReturn(new SampleObject);

        $this->assertEquals(
            'test_event_event.sample_object',
            $factory->create('test_event_event', $event->reveal())
        );
    }

    public function testShouldCreateEventNameForObjectWithGetObjectMethod(): void
    {
        $factory = $this->createEventNameFactory();
        $object = $this->prophesize(ObjectWithGetObjectMethod::class);
        $object->getObject()->willReturn(new SampleObject);

        $this->assertEquals(
            'test_event.sample_object',
            $factory->create('test_event', $object->reveal())
        );
    }

    public function testShouldCreateEventNameForObjectWithGetObjectClassMethod(): void
    {
        $factory = $this->createEventNameFactory();
        $object = $this->prophesize(ObjectWithGetObjectClassMethod::class);
        $object->getObjectClass()->willReturn('Test\TestObject');

        $this->assertEquals(
            'test_event.test_object',
            $factory->create('test_event', $object->reveal())
        );
    }

    public function testShouldCreateEventNameForObjectWithGetCollectionMethod(): void
    {
        $factory = $this->createEventNameFactory();
        $object = $this->prophesize(ObjectWithGetCollectionMethod::class);
        $object->getCollection()->willReturn([new SampleObject]);

        $this->assertEquals(
            'test_event.sample_object',
            $factory->create('test_event', $object->reveal())
        );
    }
}
