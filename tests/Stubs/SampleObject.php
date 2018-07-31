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

class SampleObject
{
    public $property1;

    private $property2;

    /**
     * @return mixed
     */
    public function getProperty1()
    {
        return $this->property1;
    }

    /**
     * @param mixed $property1
     */
    public function setProperty1($property1): void
    {
        $this->property1 = $property1;
    }

    /**
     * @return mixed
     */
    public function getProperty2()
    {
        return $this->property2;
    }

    /**
     * @param mixed $property2
     */
    public function setProperty2($property2): void
    {
        $this->property2 = $property2;
    }


}
