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

namespace Sauls\Bundle\ObjectRegistryBundle\EventDispatcher;

use function Sauls\Component\Helper\array_get_value;
use function Sauls\Component\Helper\class_ucnp;
use function Sauls\Component\Helper\object_ucnp;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var SymfonyEventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var string
     */
    private $eventNamePattern = '%s.%s';

    public function __construct(SymfonyEventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function dispatch(string $eventName, object $event): void
    {
        if ()


        if ($this->eventDispatcher->hasListeners($eventName)) {
            $event = $this->createEvent(...$arguments);
            $this->eventDispatcher->dispatch($eventName, $event);
        }
    }

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
    public function createEventNmeForCollection(string $eventName, array $collection): string
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

}
