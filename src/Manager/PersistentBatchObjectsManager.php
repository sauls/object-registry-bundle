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
use function Sauls\Component\Helper\get_object_property_value;
use function Sauls\Component\Helper\set_object_property_value;

class PersistentBatchObjectsManager implements PersistentBatchObjectsManagerInterface
{
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
     * @var array
     */
    private $refreshProperties;

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
            $this->entityManager->clear();
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
            $this->refresh($object);
            $operation->execute($object);
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

    public function setRefresh(array $properties): void
    {
        $this->refreshProperties = $properties;
    }

    /**
     * @param $object
     * @throws \Sauls\Component\Helper\Exception\PropertyNotAccessibleException
     * @throws \Sauls\Component\Helper\Exception\ClassPropertyNotSetException
     */
    private function refresh(object $object): void
    {
        if (empty($this->refreshProperties)) {
            return;
        }

        foreach ($this->refreshProperties as $property) {
            $value = get_object_property_value($object, $property);
            if (false === \is_object($value)) {
                continue;
            }

            $metadata = $this->entityManager->getClassMetadata(\get_class($value));
            $freshValue = $this->entityManager->find($metadata->getName(), $metadata->getIdentifierValues($value));

            set_object_property_value($object, $property, $freshValue);
        }
    }
}
