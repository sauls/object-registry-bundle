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

interface EventDispatcherInterface
{
    public function dispatch(string $eventName, object $event);
    public function createEventNameForClass(string $eventName, string $class): string;
    public function createEventNameForObject(string $eventName, object $object): string;
}