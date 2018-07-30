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

use Sauls\Bundle\ObjectRegistryBundle\Exception\CannotCreateEventNameForCollectionException;
use function Sauls\Component\Helper\array_get_value;
use function Sauls\Component\Helper\class_ucnp;
use function Sauls\Component\Helper\object_ucnp;

class EventNameFactory implements EventNameFactoryInterface
{
    /**
     * @var string
     */
    private $eventNamePattern = '%s.%s';

    public function create(string $eventName, object $object): string
    {
        if (\method_exists($object, 'getSubject')) {
            return $this->createEventNameForObject($eventName, $object->getSubject());
        }

        if (\method_exists($object, 'getObject')) {
            return $this->createEventNameForObject($eventName, $object->getObject());
        }

        if (\method_exists($object, 'getObjectClass')) {
            return $this->createEventNameForClass($eventName, $object->getObjectClass());
        }

        if (\method_exists($object, 'getCollection')) {
            return $this->createEventNameForCollection($eventName, $object->getCollection());
        }

        return $this->createEventNameForObject($eventName, $object);
    }

    public function createEventNameForObject(string $eventName, object $object): string
    {
        return sprintf($this->eventNamePattern, $eventName, object_ucnp($object));
    }

    public function createEventNameForClass(string $eventName, string $class): string
    {
        return sprintf($this->eventNamePattern, $eventName, class_ucnp($class));
    }

    /**
     * @param string $eventName
     * @param array $collection
     * @return string
     * @throws \Sauls\Component\Helper\Exception\PropertyNotAccessibleException
     */
    public function createEventNameForCollection(string $eventName, array $collection): string
    {
        $object = array_get_value($collection, 0);
        if (null !== $object) {
            return $this->createEventNameForObject($eventName, $object);
        }

        throw new CannotCreateEventNameForCollectionException(
            sprintf('Cannot create event name for collection: `%s`', print_r($collection, true))
        );
    }
}
