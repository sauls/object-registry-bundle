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

use Sauls\Bundle\ObjectRegistryBundle\Factory\EventNameFactoryInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var SymfonyEventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var EventNameFactoryInterface
     */
    private $eventNameFactory;

    public function __construct(
        SymfonyEventDispatcherInterface $eventDispatcher
    ) {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function dispatch(string $eventName, Event $event): void
    {
        if ($this->eventDispatcher->hasListeners($eventName)) {
            $this->eventDispatcher->dispatch($eventName, $event);
        }
    }
}
