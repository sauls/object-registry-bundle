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

namespace Sauls\Bundle\ObjectRegistryBundle\Registry;

use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Sauls\Bundle\ObjectRegistryBundle\Collection\ObjectManagerCollectionInterface;
use Sauls\Bundle\ObjectRegistryBundle\Manager\DoctrineEntityManagerInterface;
use Sauls\Bundle\ObjectRegistryBundle\Manager\ManagerInterface;

class ObjectRegistryTest extends TestCase
{
    private $objectManagerCollection;
    private $entityManager;
    private $classMetadataFactory;

    public function createObjectRegistry(): RegistryInterface
    {
        return new ObjectRegistry(
            $this->objectManagerCollection->reveal(),
            $this->entityManager->reveal()
        );
    }

    public function testShouldReturnConcreteManager(): void
    {
        $manager = $this->prophesize(ManagerInterface::class);
        $manager->setObjectClass('Test\MyClass')->shouldBeCalled();
        $this->objectManagerCollection->keyExists('Test\MyClass')->willReturn(true);
        $this->objectManagerCollection->get('Test\MyClass')->willReturn($manager->reveal());
        $registry = $this->createObjectRegistry();
        $registry->getManager('Test\MyClass');
    }

    public function testShouldReturnDoctrineManager(): void
    {
        $manager = $this->prophesize(DoctrineEntityManagerInterface::class);
        $manager->setObjectClass('Test\EntityClass')->shouldBeCalled();
        $this->objectManagerCollection->keyExists('Test\EntityClass')->willReturn(false);
        $this->classMetadataFactory->isTransient('Test\EntityClass')->willReturn(false);
        $this->objectManagerCollection->get('doctrine.object_manager')->willReturn($manager->reveal());
        $registry = $this->createObjectRegistry();
        $registry->getManager('Test\EntityClass');
    }

    public function testShouldReturnObjectManager(): void
    {
        $manager = $this->prophesize(ManagerInterface::class);
        $manager->setObjectClass('Test\AnyClass')->shouldBeCalled();
        $this->objectManagerCollection->keyExists('Test\AnyClass')->willReturn(false);
        $this->classMetadataFactory->isTransient('Test\AnyClass')->willReturn(true);
        $this->objectManagerCollection->get('default.object_manager')->willReturn($manager->reveal());
        $registry = $this->createObjectRegistry();
        $registry->getManager('Test\AnyClass');
    }

    protected function setUp()
    {
        $this->objectManagerCollection = $this->prophesize(ObjectManagerCollectionInterface::class);
        $this->classMetadataFactory = $this->prophesize(ClassMetadataFactory::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);
        $this->entityManager->getMetadataFactory()->willReturn($this->classMetadataFactory);
    }
}
