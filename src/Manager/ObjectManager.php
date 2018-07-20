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

use function Sauls\Component\Helper\define_object;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ObjectManager implements ManagerInterface, NamedManagerInterface
{
    public const DEFAULT_OBJECT_MANAGER_NAME = 'default_object_manager';

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
        return define_object($object, $properties);
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
