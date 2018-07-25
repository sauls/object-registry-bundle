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

interface ManagerInterface
{
    public function create(array $properties = []): object;
    public function modify(object $object, array $properties): object;

    public function setObjectClass(string $class): void;

    /**
     * @param mixed|object|string $value
     * @return bool
     */
    public function supports($value): bool;
    public function checkObjectIntegrity(object $object): void;
}
