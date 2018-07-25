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

use function Sauls\Component\Helper\array_get_value;
use function Sauls\Component\Helper\class_ucnp;
use function Sauls\Component\Helper\object_ucnp;

class EventNameFactory implements EventNameFactoryInterface
{
    /**
     * @var string
     */
    private $eventNamePattern = '%s.%s';

    public function createEventNameForClass(string $eventName, string $class): string
    {
        return sprintf($this->eventNamePattern, $eventName, class_ucnp($class));
    }

    public function createEventNameForObject(string $eventName, object $object): string
    {
        return sprintf($this->eventNamePattern, $eventName, object_ucnp($object));
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

        // todo: change to conrete exception class e.g: CannotCreateEventNameForCollectionException
        throw new \RuntimeException(
            sprintf('Cannot create event name for collection: `%s`', print_r($collection))
        );
    }

    public function createEventNameForEvent(string $eventName, object $event): string
    {
        if (\method_exists($event, 'getSubject')) {
            return $this->createEventNameForObject($eventName, $event->getSubject());
        }

        if (\method_exists($event, 'getObject')) {
            return $this->createEventNameForObject($eventName, $event->getObject());
        }

        if (\method_exists($event, 'getObjectClass')) {
            return $this->createEventNameForClass($eventName, $event->getObjectClass());
        }

        // todo: change exception to proper name
        throw new \RuntimeException(sprintf('Cannot create event name for event: `%s`', \get_class($event)));
    }
}
