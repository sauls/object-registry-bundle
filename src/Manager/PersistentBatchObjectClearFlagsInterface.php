<?php
/**
 * This file is part of the sauls/object-registry-bundle package.
 *
 * @author    Saulius Vaičeliūnas <vaiceliunas@inbox.lt>
 * @link      http://saulius.vaiceliunas.lt
 * @copyright 2019
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sauls\Bundle\ObjectRegistryBundle\Manager;

interface PersistentBatchObjectClearFlagsInterface
{
    public function setClearAll(): PersistentBatchObjectsManagerInterface;
    public function setClearSpecific(array $objectNames): PersistentBatchObjectsManagerInterface;
    public function setClearObject(): PersistentBatchObjectsManagerInterface;
    public function setClearNone(): PersistentBatchObjectsManagerInterface;
}
