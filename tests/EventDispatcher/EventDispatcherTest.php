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

use PHPUnit\Framework\TestCase;
use Sauls\Bundle\ObjectRegistryBundle\Factory\EventNameFactoryInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

class EventDispatcherTest extends TestCase
{
    /**
     * @var SymfonyEventDispatcherInterface
     */
    private $symfonyEventDispatcher;
    /**
     * @var EventNameFactoryInterface
     */
    private $eventNameFactory;

    public function testShouldDispatchEvent(): void
    {
        $eventDispatcher = $this->createEventDispatcher();
        $event = $this->prophesize(Event::class);


        $this->eventNameFactory->create('test_event', $event)->willReturn('test_event.test');
        $this->symfonyEventDispatcher->hasListeners('test_event.test')->willReturn(true);

        $this->symfonyEventDispatcher->dispatch('test_event.test', $event)->shouldBeCalled();

        $eventDispatcher->dispatch('test_event', $event->reveal());
    }

    public function createEventDispatcher(): EventDispatcherInterface
    {
        return new EventDispatcher(
            $this->symfonyEventDispatcher->reveal(),
            $this->eventNameFactory->reveal()
        );
    }

    protected function setUp()
    {
        $this->symfonyEventDispatcher = $this->prophesize(SymfonyEventDispatcherInterface::class);
        $this->eventNameFactory = $this->prophesize(EventNameFactoryInterface::class);
    }
}
