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

namespace Sauls\Bundle\ObjectRegistryBundle\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Sauls\Bundle\ObjectRegistryBundle\Registry\ObjectRegistry;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SaulsObjectRegistryExtensionTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    public function testShouldLoadExtension(): void
    {
        $extension = $this->createSaulsObjectRegistryExtension();
        $extension->load([], $this->containerBuilder);

        $this->assertTrue(
            $this->containerBuilder->has(ObjectRegistry::class)
        );
    }

    public function createSaulsObjectRegistryExtension(): SaulsObjectRegistryExtension
    {
        return new SaulsObjectRegistryExtension;
    }

    protected function setUp()
    {
        $this->containerBuilder = new ContainerBuilder;
    }

}
