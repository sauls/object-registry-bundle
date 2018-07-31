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

use Sauls\Bundle\ObjectRegistryBundle\Exception\UnsupportedManagerClassException;
use Sauls\Bundle\ObjectRegistryBundle\Manager\ConcreteManagerInterface;
use Sauls\Bundle\ObjectRegistryBundle\Manager\ManagerInterface;
use Sauls\Bundle\ObjectRegistryBundle\Manager\NamedManagerInterface;
use Sauls\Component\Collection\ArrayCollection;

class ObjectManagerCollection extends ArrayCollection implements ObjectManagerCollectionInterface
{
    public const LOOSE_MANAGERS_KEY = 'loose';
    public const NAMED_MANAGERS_KEY = 'named';
    public const CONCRETE_MANAGERS_KEY = 'concrete';

    public function set($key, $value): void
    {
        $this->setConcreteManager($value);
        $this->setNamedManager($value);
        $this->setLooseManager($key, $value);
        $this->throwUnsupportedManagerClassException($value);
    }

    /**
     * @param $value
     */
    private function setConcreteManager(object $value): void
    {
        if (\is_subclass_of($value, ConcreteManagerInterface::class)) {
            parent::set(
                $this->glueKeyNameWithValue(self::CONCRETE_MANAGERS_KEY, $value->getObjectClass()), $value
            );
            return;
        }
    }

    private function glueKeyNameWithValue(string $keyName, string $value): string
    {
        return \sprintf('%s.%s', $keyName, $value);
    }

    /**
     * @param $value
     */
    private function setNamedManager(object $value): void
    {
        if (\is_subclass_of($value, NamedManagerInterface::class)) {
            parent::set(
                $this->glueKeyNameWithValue(self::NAMED_MANAGERS_KEY, $value->getName()), $value
            );
            return;
        }
    }

    /**
     * @param $key
     * @param $value
     */
    private function setLooseManager($key, object $value): void
    {
        if (\is_subclass_of($value, ManagerInterface::class)) {
            parent::set(
                $this->glueKeyNameWithValue(self::LOOSE_MANAGERS_KEY, $key), $value
            );
            return;
        }
    }

    /**
     * @param $value
     */
    private function throwUnsupportedManagerClassException(object $value): void
    {
        if ($this->isNotSupportedObjectClass($value)) {
            throw new UnsupportedManagerClassException(
                sprintf(
                    'Given `%s` class should implement one of [%s] interfaces',
                    \get_class($value),
                    \implode(', ', $this->getSupportedManagerClasses())
                )
            );
        }
    }

    private function isNotSupportedObjectClass(object $value): bool
    {
        return false === $this->isSupportedObjectClass($value);
    }

    private function isSupportedObjectClass(object $value): bool
    {
        foreach ($this->getSupportedManagerClasses() as $supportedObjectClass) {
            if ($value instanceof $supportedObjectClass) {
                return true;
            }
        }

        return false;
    }

    private function getSupportedManagerClasses(): array
    {
        return [
            ManagerInterface::class,
            ConcreteManagerInterface::class,
            NamedManagerInterface::class
        ];
    }

    public function get($key, $default = null)
    {
        foreach ($this->getSupportedManagerGroups() as $managerGroup) {
            $managerGroupKey = $this->glueKeyNameWithValue($managerGroup, $key);
            if ($this->keyExists($managerGroupKey)) {
                return parent::get($managerGroupKey, $default);
            }
        }

        return $default;
    }

    private function getSupportedManagerGroups(): array
    {
        return [
            self::CONCRETE_MANAGERS_KEY,
            self::NAMED_MANAGERS_KEY,
            self::LOOSE_MANAGERS_KEY
        ];
    }

    public function count(): int
    {
        $total = 0;

        foreach ($this->all() as $group => $managers) {
            $total += \count($managers);
        }

        return $total;
    }

    public function clear(): void
    {
        foreach ($this->getSupportedManagerGroups() as $group) {
            parent::set($group, []);
        }
    }

}
