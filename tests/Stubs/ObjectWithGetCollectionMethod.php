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


namespace Sauls\Bundle\ObjectRegistryBundle\Stubs;

class ObjectWithGetCollectionMethod
{
    /**
     * @var array
     */
    private $collection;

    public function __construct(array $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return array
     */
    public function getCollection(): array
    {
        return $this->collection;
    }
}
