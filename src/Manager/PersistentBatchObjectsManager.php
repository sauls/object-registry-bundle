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
use Psr\Log\LoggerInterface;
use Sauls\Bundle\ObjectRegistryBundle\Batch\Operation\OperationInterface;
use Sauls\Bundle\ObjectRegistryBundle\Batch\Operation\PersistOperation;
use Sauls\Bundle\ObjectRegistryBundle\Batch\Operation\RemoveOperation;
use Sauls\Bundle\ObjectRegistryBundle\Collection\BatchOperationCollectionInterface;
use Sauls\Bundle\ObjectRegistryBundle\Event\GenericDoctrineCollectionEvent;
use Sauls\Bundle\ObjectRegistryBundle\EventDispatcher\EventDispatcherInterface;
use Sauls\Bundle\ObjectRegistryBundle\Exception\EmptyDataException;
use Sauls\Bundle\ObjectRegistryBundle\Exception\ManagerNotFoundException;
use Sauls\Bundle\ObjectRegistryBundle\Exception\OperationNotFoundException;

class PersistentBatchObjectsManager implements PersistentBatchObjectsManagerInterface
{
    private const CLEAR_NONE = 'none';
    private const CLEAR_ALL = 'all';
    private const CLEAR_OBJECT = 'object';
    private const CLEAR_SPECIFIC = 'specific';

    /**
     * @var array
     */
    private $chunks;

    /**
     * @var DoctrineEntityManagerInterface
     */
    private $manager;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var BatchOperationCollectionInterface
     */
    private $batchOperations;

    /**
     * @var string
     */
    private $clearState = self::CLEAR_ALL;

    /**
     * @var array
     */
    private $clearSpecificObjectNames = [];

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        BatchOperationCollectionInterface $batchOperations,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->batchOperations = $batchOperations;
        $this->logger = $logger;
    }

    public function save(): bool
    {
        return $this->process(PersistOperation::NAME);
    }

    private function process(string $operationName): bool
    {
        $this->checkManagerIsNotNull();
        $this->checkChunksIsNotEmpty();

        try {
            $self = $this;
            $this->entityManager->transactional(function () use ($operationName, $self) {
                $self->processChunks($operationName);
            });
            return true;
        } catch (\Throwable $exception) {
            $this->logger->critical($exception->getMessage(), [$exception]);
            return false;
        }
    }

    private function checkManagerIsNotNull(): void
    {
        if (null === $this->manager) {
            throw new ManagerNotFoundException(
                'Manager cannot be `null` maybe you forgot to assign it? To do so use `setManager` method'
            );
        }
    }

    private function checkChunksIsNotEmpty(): void
    {
        if (true === empty($this->chunks)) {
            throw new EmptyDataException(
                \sprintf('No data to work with maybe you forgot to fill?')
            );
        }
    }

    private function processChunks(string $operationName): void
    {
        $operation = $this->getOperationByName($operationName);

        foreach ($this->chunks as $chunk) {
            $event = new GenericDoctrineCollectionEvent($chunk);
            $this->eventDispatcher->dispatch($operation->getPreEventName(), $event);
            $this->processChunk($operation, $chunk);
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch($operation->getPostEventName(), $event);
            $this->clear();
        }
    }

    private function getOperationByName(string $operationName): OperationInterface
    {
        $operation = $this->batchOperations->get($operationName);

        if (null === $operation) {
            throw new OperationNotFoundException(sprintf('Batch operation `%s` was not found', $operationName));
        }

        return $operation;
    }

    private function processChunk(OperationInterface $operation, array $chunk): void
    {
        foreach ($chunk as $object) {
            $this->manager->checkObjectIntegrity($object);
            $operation->execute($object);

            if ($this->isState(self::CLEAR_OBJECT)) {
                $this->setSpecificObjectName(\get_class($object));
            }
        }
    }

    private function isState(string $state): bool
    {
        return true === ($state === $this->clearState);
    }

    private function setSpecificObjectName(string $objectName): void
    {
        if (false === \in_array($objectName, $this->clearSpecificObjectNames)) {
            $this->clearSpecificObjectNames[] = $objectName;
        }
    }

    private function addSpecificObjectNames(array $objectNames): void
    {
        foreach ($objectNames as $objectName) {
            $this->setSpecificObjectName($objectName);
        }
    }

    private function clear(): void
    {
        switch ($this->clearState) {
            case self::CLEAR_ALL:
                $this->entityManager->clear();
                break;
            case self::CLEAR_OBJECT:
            case self::CLEAR_SPECIFIC:
                foreach ($this->clearSpecificObjectNames as $objectName) {
                    $this->entityManager->clear($objectName);
                }
                break;
            case self::CLEAR_NONE:
                break;
        }
    }

    public function remove(): bool
    {
        return $this->process(RemoveOperation::NAME);
    }

    public function fill(array $objects, int $batchSize): void
    {
        $this->chunks = $this->splitToChunks($objects, $batchSize);
    }

    private function splitToChunks(array $objects, int $batchSize): array
    {
        return \array_chunk($objects, $batchSize);
    }

    public function setManager(DoctrineEntityManagerInterface $manager): void
    {
        $this->manager = $manager;
    }

    public function setClearAll(): PersistentBatchObjectsManagerInterface
    {
        $this->clearState = self::CLEAR_ALL;

        return $this;
    }

    public function setClearSpecific(array $objectNames): PersistentBatchObjectsManagerInterface
    {
        $this->clearState = self::CLEAR_SPECIFIC;
        $this->addSpecificObjectNames($objectNames);

        return $this;
    }

    public function setClearObject(): PersistentBatchObjectsManagerInterface
    {
        $this->clearState = self::CLEAR_OBJECT;

        return $this;
    }

    public function setClearNone(): PersistentBatchObjectsManagerInterface
    {
        $this->clearState = self::CLEAR_NONE;

        return $this;
    }
}
