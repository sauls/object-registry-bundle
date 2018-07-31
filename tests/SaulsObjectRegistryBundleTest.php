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

namespace Sauls\Bundle\ObjectRegistryBundle;

use PHPUnit\Framework\TestCase;
use Sauls\Bundle\ObjectRegistryBundle\DependencyInjection\Compiler\RegisterTaggedServicesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SaulsObjectRegistryBundleTest extends TestCase
{
    public function testShouldAddCompilerPass(): void
    {
        $bundle = new SaulsObjectRegistryBundle;
        $containerBuilder = $this->prophesize(ContainerBuilder::class);

        $containerBuilder->addCompilerPass(new RegisterTaggedServicesPass())->shouldBeCalled();

        $bundle->build($containerBuilder->reveal());
    }
}
