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

use Sauls\Bundle\ObjectRegistryBundle\EventDispatcher\EventDispatcherInterface;
use Sauls\Bundle\ObjectRegistryBundle\Exception\UnsupportedObjectClassException;
use function Sauls\Component\Helper\define_object;

class ObjectManager implements ManagerInterface, NamedManagerInterface
{
    public const DEFAULT_OBJECT_MANAGER_NAME = 'default.object_manager';

    /**
     * @var string
     */
    protected $objectClass;
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param array $properties
     * @return object
     * @throws \Sauls\Component\Helper\Exception\PropertyNotAccessibleException
     */
    public function create(array $properties = []): object
    {
        return $this->modify(new $this->objectClass, $properties);
    }

    /**
     * @param object $object
     * @param array $properties
     * @return object
     * @throws \Sauls\Component\Helper\Exception\PropertyNotAccessibleException
     */
    public function modify(object $object, array $properties): object
    {
        $this->checkObjectIntegrity($object);

        return define_object($object, $properties);
    }

    public function checkObjectIntegrity(object $object): void
    {
        if ($this->notSupported($object)) {
            throw new UnsupportedObjectClassException(
                sprintf(
                    'Object manager of `%s` object class does not support given `%s` class',
                    $this->objectClass,
                    \get_class($object)
                )
            );
        }
    }

    private function classIsSupported(string $class): bool
    {
        return false === empty($this->objectClass) && \is_a($class, $this->objectClass, true);
    }

    private function objectIsSupported(object $object): bool
    {
        return $object instanceof $this->objectClass;
    }

    private function notSupported($value): bool
    {
        return false === $this->supports($value);
    }

    /**
     * @param mixed|object|string $value
     * @return bool
     */
    public function supports($value): bool
    {
        if (\is_string($value)) {
            return $this->classIsSupported($value);
        }

        if (\is_object($value)) {
            return $this->objectIsSupported($value);
        }

        return false;
    }

    public function getName(): string
    {
        return self::DEFAULT_OBJECT_MANAGER_NAME;
    }

    public function setObjectClass(string $class): void
    {
        $this->objectClass = $class;
    }
}
