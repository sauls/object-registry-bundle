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

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Sauls\Bundle\ObjectRegistryBundle\Batch\Operation\OperationInterface;
use Sauls\Bundle\ObjectRegistryBundle\Collection\BatchOperationCollectionInterface;
use Sauls\Bundle\ObjectRegistryBundle\Event\GenericDoctrineCollectionEvent;
use Sauls\Bundle\ObjectRegistryBundle\EventDispatcher\EventDispatcherInterface;
use Sauls\Bundle\ObjectRegistryBundle\Exception\EmptyDataException;
use Sauls\Bundle\ObjectRegistryBundle\Exception\ManagerNotFoundException;
use Sauls\Bundle\ObjectRegistryBundle\Exception\OperationNotFoundException;
use Sauls\Bundle\ObjectRegistryBundle\Stubs\SampleObject;

class PersistentBatchObjectsManagerTest extends TestCase
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var BatchOperationCollectionInterface
     */
    private $batchOperationCollection;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var DoctrineEntityManagerInterface
     */
    private $doctrineManager;

    public function testShouldThrowManagerNotFoundException(): void
    {
        $this->expectException(ManagerNotFoundException::class);
        $manager = $this->createPersistentBatchObjectsManager();
        $manager->save();
    }

    public function createPersistentBatchObjectsManager(): PersistentBatchObjectsManagerInterface
    {
        return new PersistentBatchObjectsManager(
            $this->entityManager->reveal(),
            $this->eventDispatcher->reveal(),
            $this->batchOperationCollection->reveal(),
            $this->logger->reveal()
        );
    }

    public function testShouldThrowEmptyDataException(): void
    {
        $this->expectException(EmptyDataException::class);
        $manager = $this->createPersistentBatchObjectsManager();
        $manager->setManager($this->doctrineManager->reveal());
        $manager->save();
    }

    public function testShouldReturnFalseWhenExceptionIsThrown(): void
    {
        $manager = $this->configureManager([new \stdClass], 2);
        $exception = new \Exception('Failed!');
        $this->entityManager->transactional(Argument::any())->willThrow($exception);
        $this->logger->critical('Failed!', [$exception])->shouldBeCalled();

        $this->assertFalse($manager->save());
    }

    /**
     * @param array $objects
     * @param int $batchSize
     * @return PersistentBatchObjectsManagerInterface
     */
    private function configureManager(array $objects, int $batchSize): PersistentBatchObjectsManagerInterface
    {
        $manager = $this->createPersistentBatchObjectsManager();
        $manager->fill($objects, $batchSize);
        $manager->setManager($this->doctrineManager->reveal());
        return $manager;
    }

    public function testShouldSave(): void
    {
        $manager = $this->configureManager([new \stdClass], 2);
        $operation = $this->configureOperation('persist', 'test_pre_persist', 'test_post_persist');
        $operation->execute(Argument::any())->shouldBeCalled();
        $this->batchOperationCollection->get('persist')->willReturn($operation->reveal());

        $this->configureProcessMethodsShouldBeCalled('test_pre_persist', 'test_post_persist');

        $this->entityManager->transactional(Argument::any())->will(function ($args) {
            return \call_user_func($args[0]);
        });

        $this->assertTrue($manager->save());
    }

    private function configureOperation(string $name, string $preEventName, string $postEventName)
    {
        $operation = $this->prophesize(OperationInterface::class);
        $operation->getName()->willReturn($name);
        $operation->getPreEventName()->willReturn($preEventName);
        $operation->getPostEventName()->willReturn($postEventName);

        return $operation;
    }

    private function configureProcessMethodsShouldBeCalled($preEventName, string $postEventName): void
    {
        $this->eventDispatcher->dispatch($preEventName,
            Argument::type(GenericDoctrineCollectionEvent::class))->shouldBeCalled();
        $this->entityManager->flush()->shouldBeCalled();
        $this->eventDispatcher->dispatch($postEventName,
            Argument::type(GenericDoctrineCollectionEvent::class))->shouldBeCalled();
        $this->entityManager->clear()->shouldBeCalled();
    }

    public function testShouldRemove(): void
    {
        $manager = $this->configureManager([new \stdClass], 2);
        $operation = $this->configureOperation('remove', 'test_pre_remove', 'test_post_remove');
        $operation->execute(Argument::any())->shouldBeCalled();
        $this->batchOperationCollection->get('remove')->willReturn($operation->reveal());

        $this->configureProcessMethodsShouldBeCalled('test_pre_remove', 'test_post_remove');

        $this->entityManager->transactional(Argument::any())->will(function ($args) {
            return \call_user_func($args[0]);
        });

        $this->assertTrue($manager->remove());
    }

    public function testShouldThrowExceptionWhenOperationDoesNotExists(): void
    {
        $manager = $this->configureManager([new \stdClass], 2);
        $this->batchOperationCollection->get(Argument::any())->willReturn(null);
        $this->entityManager->transactional(Argument::any())->will(function ($args) {
            return \call_user_func($args[0]);
        });

        $this->logger->critical(
            'Batch operation `persist` was not found',
            Argument::withEntry(
                0, Argument::type(OperationNotFoundException::class)
            )
        )->shouldBeCalled();

        $manager->save();
    }

    public function testShouldRefresh(): void
    {
        $object2 = new SampleObject;

        $object1 = new SampleObject;
        $object1->setProperty1($object2);

        $manager = $this->configureManager([$object2, $object1], 5);
        $operation = $this->configureOperation('persist', 'test_pre_persist', 'test_post_persist');
        $operation->execute(Argument::any())->shouldBeCalled();
        $this->batchOperationCollection->get('persist')->willReturn($operation->reveal());

        $this->configureProcessMethodsShouldBeCalled('test_pre_persist', 'test_post_persist');

        $this->entityManager->transactional(Argument::any())->will(function ($args) {
            return \call_user_func($args[0]);
        });

        $medatataMock = $this->prophesize(ClassMetadata::class);
        $medatataMock->getName()->willReturn('id');
        $medatataMock->getIdentifierValues(Argument::any())->willReturn(1);
        $this->entityManager->getClassMetadata(SampleObject::class)->willReturn($medatataMock->reveal());
        $this->entityManager->find('id', 1)->willReturn($object2);

        $manager->setRefresh([
            'property1'
        ]);

        $manager->save();
        $this->assertTrue($manager->save());
    }

    protected function setUp()
    {
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->batchOperationCollection = $this->prophesize(BatchOperationCollectionInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->doctrineManager = $this->prophesize(DoctrineEntityManagerInterface::class);
    }


}
