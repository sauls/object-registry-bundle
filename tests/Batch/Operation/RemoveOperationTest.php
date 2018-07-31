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

namespace Sauls\Bundle\ObjectRegistryBundle\Batch\Operation;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class RemoveOperationTest extends TestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function testShouldExecute(): void
    {
        $this->entityManager->remove(Argument::any())->shouldBeCalled();
        $operation = $this->createRemoveOperation();
        $operation->execute(new \stdClass());
    }

    public function createRemoveOperation(): OperationInterface
    {
        return new RemoveOperation(
            $this->entityManager->reveal()
        );
    }

    public function testShouldReturnOperationName(): void
    {
        $operation = $this->createRemoveOperation();

        $this->assertEquals('remove', $operation->getName());
    }

    public function testShouldReturnOperationPreEventName(): void
    {
        $operation = $this->createRemoveOperation();

        $this->assertEquals(
            'sauls.object_registry.event.pre_doctrine_object_batch_remove',
            $operation->getPreEventName()
        );
    }

    public function testShouldReturnOperationPostEventName(): void
    {
        $operation = $this->createRemoveOperation();

        $this->assertEquals(
            'sauls.object_registry.event.post_doctrine_object_batch_remove',
            $operation->getPostEventName()
        );
    }

    protected function setUp()
    {
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
    }
}
