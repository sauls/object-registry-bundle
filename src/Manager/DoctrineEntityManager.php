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
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use Psr\Log\LoggerInterface;
use Sauls\Bundle\ObjectRegistryBundle\EventDispatcher\EventDispatcherInterface;
use Sauls\Bundle\ObjectRegistryBundle\Exception\UnsupportedObjectClassException;

class DoctrineEntityManager extends ObjectManager implements DoctrineEntityManagerInterface
{
    public const DOCTRINE_OBJECT_MANAGER_NAME = 'doctrine.object_manager';

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ClassMetadataFactory
     */
    private $classMetadataFactory;
    /**
     * @var PersistentBatchObjectsManagerInterface
     */
    private $persistentBatchObjectsManager;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        PersistentBatchObjectsManagerInterface $persistentBatchObjectsManager
    ) {
        parent::__construct($eventDispatcher);

        $this->eventDispatcher = $eventDispatcher;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->classMetadataFactory = $entityManager->getMetadataFactory();
        $this->persistentBatchObjectsManager = $persistentBatchObjectsManager;
    }

    public function save(object $object): bool
    {
        try {

            $this->checkObjectIntegrity($object);

            $this->entityManager->persist($object);
            $this->entityManager->flush();

            return true;
        } catch (\Throwable $exception) {
            $this->logger->critical($exception->getMessage(), [$exception]);
            return false;
        }
    }

    public function remove(object $object): bool
    {
        try {
            $this->checkObjectIntegrity($object);

            $this->entityManager->remove($object);
            $this->entityManager->flush();

            return true;
        } catch (\Throwable $exception) {
            $this->logger->critical($exception->getMessage(), [$exception]);
            return false;
        }
    }

    public function setObjectClass(string $class): void
    {
        if ($this->classIsNotEntity($class)) {
            throw new UnsupportedObjectClassException(
                sprintf('Class of `%s` is not of Entity class', $class)
            );
        }

        parent::setObjectClass($class);
    }

    private function classIsNotEntity(string $class): bool
    {
        return true === $this->classMetadataFactory->isTransient($class);
    }

    public function getName(): string
    {
        return self::DOCTRINE_OBJECT_MANAGER_NAME;
    }

    public function batch(
        array $objects,
        int $batchSize = self::DEFAULT_BATCH_SIZE
    ): PersistentBatchObjectsManagerInterface {
        $this->persistentBatchObjectsManager->fill($objects, $batchSize);
        $this->persistentBatchObjectsManager->setManager($this);
        return $this->persistentBatchObjectsManager;
    }

    public function clear(): void
    {
        $this->entityManager->clear($this->objectClass);
    }
}
