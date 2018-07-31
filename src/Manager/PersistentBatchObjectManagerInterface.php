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

interface PersistentBatchObjectManagerInterface
{
    public const DEFAULT_BATCH_SIZE = 50;

    public function batch(
        array $objects,
        int $batchSize = self::DEFAULT_BATCH_SIZE
    ): PersistentBatchObjectsManagerInterface;
}
