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

interface PersistentBatchObjectsManagerInterface extends PersistentBatchObjectClearFlagsInterface
{
    public function save(): bool;
    public function remove(): bool;
    public function fill(array $objects, int $batchSize): void;
    public function setManager(DoctrineEntityManagerInterface $manager): void;
}
