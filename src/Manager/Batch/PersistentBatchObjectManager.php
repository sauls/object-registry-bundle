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

namespace Sauls\Bundle\ObjectRegistryBundle\Manager\Batch;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sauls\Bundle\ObjectRegistryBundle\Event\GenericDoctrineCollectionEvent;
use Sauls\Bundle\ObjectRegistryBundle\EventDispatcher\EventDispatcherInterface;
use Sauls\Bundle\ObjectRegistryBundle\Manager\DoctrineEntityManagerInterface;

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
     * @var BatchOperationCollection
     */
    private $batchOperations;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        BatchOperationCollection $batchOperations,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->batchOperations = $batchOperations;
        $this->logger = $logger;
    }

    public function save(): bool
    {
        return $this->process(BatchOperations::PERSIST_OPERATION);
    }

    private function process(string $operationName): bool
    {
        $this->checkManagerIsNotNull();
        $this->checkChunksIsNotEmpty();

        try {
            $this->entityManager->transactional(function () use ($operationName) {
                $this->processChunks($operationName);
            });
            return true;
        } catch (\Throwable $exception) {
            return false;
        }
    }

    private function checkManagerIsNotNull(): void
    {
        if (null === $this->manager) {
            throw new \RuntimeException(
                sprintf('Manager cannot be `null` maybe you forgot to assign it?')
            );
        }
    }

    private function checkChunksIsNotEmpty(): void
    {
        if (true === empty($this->chunks)) {
            throw new \RuntimeException(
                \sprintf('No data to work with maybe you forgot to fill?')
            );
        }
    }

    private function processChunks(string $operationName): void
    {
        $operation = $this->batchOperations->get($operationName);

        foreach ($this->chunks as $chunk) {
            $event = new GenericDoctrineCollectionEvent($chunk);
            $this->eventDispatcher->dispatch($operation->getPreEventName(), $event);
            $this->processChunk($operation, $chunk);
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch($operation->getPostEventName(), $event);
            $this->entityManager->clear();
        }
    }

    private function processChunk(BatchOperationInterface $operation, array $chunk): void
    {
        foreach ($chunk as $object) {
            $this->manager->checkObjectIntegrity($object);
            $operation->execute($object);
        }
    }

    public function remove(): bool
    {
        return $this->process(BatchOperations::REMOVE_OPERATION);
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
}
