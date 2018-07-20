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
use Sauls\Bundle\ObjectRegistryBundle\Stubs\Account;
use Sauls\Bundle\ObjectRegistryBundle\Stubs\SampleObject;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;

class EventDispatcherTest extends TestCase
{
    protected $symfonyEventDispatcher;

    public function testShouldCreateEventFromGivenObjectClass(): void
    {
        $eventDispatcher = $this->createEventDispatcher();
        $this->assertEquals(
            'test.event.sample_object',
            $eventDispatcher->createEventNameForClass('test.event', SampleObject::class)
        );
    }

    public function testShouldCreateEventFromGivenObject(): void
    {
        $eventDispatcher = $this->createEventDispatcher();
        $object = new Account;

        $this->assertEquals(
            'test.event.account',
            $eventDispatcher->createEventNameForObject('test.event', $object)
        );


    }

    public function createEventDispatcher(): EventDispatcherInterface
    {
        $this->symfonyEventDispatcher = $this->prophesize(SymfonyEventDispatcherInterface::class);
        return new EventDispatcher($this->symfonyEventDispatcher->reveal());
    }
}
