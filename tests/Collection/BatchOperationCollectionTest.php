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

use PHPUnit\Framework\TestCase;
use Sauls\Bundle\ObjectRegistryBundle\Batch\Operation\OperationInterface;
use Sauls\Bundle\ObjectRegistryBundle\Exception\UnsupportedBatchOperationException;

class BatchOperationCollectionTest extends TestCase
{
    public function testCollectionShouldContainGivenOperations(): void
    {
        $operationA = $this->prophesize(OperationInterface::class);
        $operationA->getName()->willReturn('a_operation');

        $collection = $this->createBatchOperationCollection([
            $operationA->reveal(),
        ]);

        $this->assertTrue($collection->keyExists('a_operation'));
    }

    public function createBatchOperationCollection(array $operations = []): BatchOperationCollectionInterface
    {
        return new BatchOperationCollection($operations);
    }

    public function testShouldThrowUnsupportedOperationObjectExceptionWhenGivenNotOperationObject(): void
    {
        $this->expectException(UnsupportedBatchOperationException::class);
        $this->createBatchOperationCollection([
            new \stdClass,
        ]);
    }
}
