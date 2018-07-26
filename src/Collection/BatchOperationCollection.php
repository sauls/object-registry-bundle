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

namespace Sauls\Bundle\ObjectRegistryBundle\Collection;

use Sauls\Bundle\ObjectRegistryBundle\Batch\Operation\OperationInterface;
use Sauls\Bundle\ObjectRegistryBundle\Exception\UnsupportedBatchOperationException;
use Sauls\Component\Collection\ArrayCollection;

class BatchOperationCollection extends ArrayCollection implements BatchOperationCollectionInterface
{
    /**
     * @param $key
     * @param OperationInterface $value
     */
    public function set($key, $value): void
    {
        $this->checkValueIsValidOperationObject($value);

        parent::set($value->getName(), $value);
    }

    /**
     * @param $value
     */
    private function checkValueIsValidOperationObject($value): void
    {
        if ($this->isNotOperationObject($value)) {
            throw new UnsupportedBatchOperationException(
                sprintf(
                    'Given batch operation of class `%s` should implement %s interface',
                    \get_class($value),
                    OperationInterface::class)
            );
        }
    }

    private function isNotOperationObject($value): bool
    {
        return false === \is_subclass_of($value, OperationInterface::class);
    }
}
