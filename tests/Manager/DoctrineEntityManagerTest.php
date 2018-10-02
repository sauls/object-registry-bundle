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
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Sauls\Bundle\ObjectRegistryBundle\EventDispatcher\EventDispatcherInterface;
use Sauls\Bundle\ObjectRegistryBundle\Exception\UnsupportedObjectClassException;
use Sauls\Bundle\ObjectRegistryBundle\Stubs\SampleObject;

class DoctrineEntityManagerTest extends TestCase
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var PersistentBatchObjectsManagerInterface
     */
    private $batchObjectsManager;

    /**
     * @var ClassMetadataFactory
     */
    private $classMetaDataFactory;

    public function testDoctrineEntityManagerShouldReturnItsDefaultName(): void
    {
        $manager = $this->createDoctrineEntityManager(SampleObject::class);
        $this->assertEquals('doctrine.object_manager', $manager->getName());
    }

    /**
     * @param string $class
     * @return DoctrineEntityManagerInterface|ManagerInterface|NamedManagerInterface
     */
    public function createDoctrineEntityManager(string $class): DoctrineEntityManagerInterface
    {
        $manager = new DoctrineEntityManager(
            $this->eventDispatcher->reveal(),
            $this->entityManager->reveal(),
            $this->logger->reveal(),
            $this->batchObjectsManager->reveal()
        );
        $manager->setObjectClass($class);

        return $manager;
    }

    public function testShouldThrowUnsupportedObjectClassException(): void
    {
        $this->expectException(UnsupportedObjectClassException::class);
        $this->classMetaDataFactory->isTransient(Argument::any())->willReturn(true);
        $this->createDoctrineEntityManager(SampleObject::class);
    }

    public function testShouldSaveGivenObject(): void
    {
        $manager = $this->createDoctrineEntityManager(SampleObject::class);
        $object = $manager->create();

        $this->entityManager->persist($object)->shouldBeCalled();
        $this->entityManager->flush()->shouldBeCalled();

        $this->assertTrue($manager->save($object));
    }

    public function testShouldNotSaveGivenObject(): void
    {
        $manager = $this->createDoctrineEntityManager(SampleObject::class);
        $object = $manager->create();
        $exception = new \RuntimeException('Error flushing entity manager');

        $this->entityManager->persist($object)->shouldBeCalled();
        $this->entityManager->flush()->willThrow($exception);

        $this->logger->critical('Error flushing entity manager', [$exception])->shouldBeCalled();

        $this->assertFalse($manager->save($object));
    }

    public function testShouldRemoveGivenObject(): void
    {
        $manager = $this->createDoctrineEntityManager(SampleObject::class);
        $object = $manager->create();

        $this->entityManager->remove($object)->shouldBeCalled();
        $this->entityManager->flush()->shouldBeCalled();

        $this->assertTrue($manager->remove($object));
    }

    public function testShouldClearEntitiesOfManagedClass(): void
    {
        $manager = $this->createDoctrineEntityManager(SampleObject::class);

        $this->entityManager->clear(SampleObject::class)->shouldBeCalled();

        $manager->clear();
    }

    public function testShouldNotRemoveGivenObject(): void
    {
        $manager = $this->createDoctrineEntityManager(SampleObject::class);
        $object = $manager->create();
        $exception = new \RuntimeException('Error flushing entity manager');

        $this->entityManager->remove($object)->shouldBeCalled();
        $this->entityManager->flush()->willThrow($exception);

        $this->logger->critical('Error flushing entity manager', [$exception])->shouldBeCalled();

        $this->assertFalse($manager->remove($object));
    }

    public function testShouldReturnPersistenBatchObjectsManagerOnBatchMethodCall(): void
    {
        $manager = $this->createDoctrineEntityManager(SampleObject::class);
        $collection = ['1', '2'];
        $this->batchObjectsManager->fill($collection, 1)->shouldBeCalled();
        $this->batchObjectsManager->setManager($manager)->shouldBeCalled();

        $this->assertInstanceOf(
            PersistentBatchObjectsManagerInterface::class,
            $manager->batch($collection, 1)
        );
    }


    protected function setUp()
    {
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->classMetaDataFactory = $this->prophesize(ClassMetadataFactory::class);
        $this->classMetaDataFactory->isTransient(Argument::any())->willReturn(false);
        $this->batchObjectsManager = $this->prophesize(PersistentBatchObjectsManagerInterface::class);
        $this->entityManager->getMetadataFactory()->willReturn($this->classMetaDataFactory);
    }
}
