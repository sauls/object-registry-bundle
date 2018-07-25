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
use Sauls\Bundle\ObjectRegistryBundle\Collection\ObjectManagerCollectionInterface;
use Sauls\Bundle\ObjectRegistryBundle\Manager\DoctrineEntityManager;
use Sauls\Bundle\ObjectRegistryBundle\Manager\ManagerInterface;
use Sauls\Bundle\ObjectRegistryBundle\Manager\ObjectManager;
use Sauls\Component\Collection\ArrayCollection;

class ObjectRegistry implements RegistryInterface
{
    /**
     * @var ObjectManagerCollectionInterface|ArrayCollection
     */
    private $objectManagerCollection;
    /**
     * @var ClassMetadataFactory
     */
    private $classMetadataFactory;

    public function __construct(
        ObjectManagerCollectionInterface $objectManagerCollection,
        ClassMetadataFactory $classMetadataFactory
    ) {
        $this->objectManagerCollection = $objectManagerCollection;
        $this->classMetadataFactory = $classMetadataFactory;
    }

    public function getManager(string $class): ManagerInterface
    {
        if ($this->hasConcreteManager($class)) {
            return $this->configureManager(
                $class,
                $this->objectManagerCollection->get($class)
            );
        }

        if ($this->isDoctrineObject($class)) {
            return $this->configureManager(
                $class,
                $this->objectManagerCollection->get(DoctrineEntityManager::DOCTRINE_OBJECT_MANAGER_NAME)
            );
        }

        return $this->configureManager(
            $class,
            $this->objectManagerCollection->get(ObjectManager::DEFAULT_OBJECT_MANAGER_NAME)
        );
    }

    public function hasConcreteManager(string $class): bool
    {
        return $this->objectManagerCollection->keyExists($class);
    }

    private function configureManager(string $class, ManagerInterface $manager): ManagerInterface
    {
        $manager->setObjectClass($class);
        return $manager;
    }

    public function isDoctrineObject(string $class): bool
    {
        $this->classMetadataFactory->isTransient($class);
    }
}
