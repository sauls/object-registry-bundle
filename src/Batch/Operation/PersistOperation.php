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

namespace Sauls\Bundle\ObjectRegistryBundle\Batch\Operation;

use Doctrine\ORM\EntityManagerInterface;
use Sauls\Bundle\ObjectRegistryBundle\Event\DoctrineObjectEvents;

class PersistOperation implements OperationInterface
{
    public const NAME = 'persist';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function execute(object $object): void
    {
        $this->entityManager->persist($object);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getPreEventName(): string
    {
        return DoctrineObjectEvents::PRE_BATCH_SAVE;
    }

    public function getPostEventName(): string
    {
        return DoctrineObjectEvents::POST_BATCH_SAVE;
    }
}
