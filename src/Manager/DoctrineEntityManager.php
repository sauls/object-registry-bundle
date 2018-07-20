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
use Symfony\Component\EventDispatcher\EventDispatcher;

class DoctrineEntityManager extends ObjectManager implements
    PersistentObjectManagerInterface,
    PersistentBatchObjectManagerInterface
{
    public const DOCTRINE_OBJECT_MANAGER_NAME = 'doctrine_object_manager';

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(
        string $objectClass,
        EventDispatcher $eventDispatcher,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($eventDispatcher);

        $this->entityManager = $entityManager;
    }

    public function batchSave(array $objects): bool
    {
        // TODO: Implement batchSave() method.
    }

    public function batchRemove(array $objects): bool
    {
        // TODO: Implement batchRemove() method.
    }

    public function save(object $object): bool
    {
        // TODO: Implement save() method.
    }

    public function remove(object $object): bool
    {
        // TODO: Implement remove() method.
    }

    public function getName(): string
    {
        return self::DOCTRINE_OBJECT_MANAGER_NAME;
    }


}
