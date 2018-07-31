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

namespace Sauls\Bundle\ObjectRegistryBundle\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Sauls\Bundle\ObjectRegistryBundle\Batch\Operation\PersistOperation;
use Sauls\Bundle\ObjectRegistryBundle\Collection\BatchOperationCollection;
use Sauls\Bundle\ObjectRegistryBundle\Collection\ObjectManagerCollection;
use Sauls\Bundle\ObjectRegistryBundle\Manager\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterTaggedServicesPassTest extends TestCase
{
    public function testShouldProcessTaggedServices(): void
    {
        $compilerPass = new RegisterTaggedServicesPass;
        $container = $this->prophesize(ContainerBuilder::class);

        $definition = $this->prophesize(Definition::class);
        $definition->addMethodCall('set', Argument::any())->shouldBeCalled();

        $container->has(ObjectManagerCollection::class)->shouldBeCalled()->willReturn(true);
        $container->findDefinition(ObjectManagerCollection::class)->shouldBeCalled()->willReturn($definition);
        $container->findTaggedServiceIds('sauls.object_registry.manager')->shouldBeCalled()->willReturn([ObjectManager::class => []]);

        $container->has(BatchOperationCollection::class)->shouldBeCalled()->willReturn(true);
        $container->findDefinition(BatchOperationCollection::class)->shouldBeCalled()->willReturn($definition);
        $container->findTaggedServiceIds('sauls.object_registry.batch_operation')->shouldBeCalled()->willReturn([PersistOperation::class => []]);

        $compilerPass->process($container->reveal());
    }

    public function testShouldNotRegisterTaggedServicesIfCollectionClassDoesNotExist(): void
    {
        $compilerPass = new RegisterTaggedServicesPass;
        $container = $this->prophesize(ContainerBuilder::class);

        $container->has(ObjectManagerCollection::class)->shouldBeCalled()->willReturn(false);
        $container->findDefinition(ObjectManagerCollection::class)->shouldNotBeCalled();
        $container->findTaggedServiceIds('sauls.object_registry.manager')->shouldNotBeCalled();

        $container->has(BatchOperationCollection::class)->shouldBeCalled()->willReturn(false);
        $container->findDefinition(BatchOperationCollection::class)->shouldNotBeCalled();
        $container->findTaggedServiceIds('sauls.object_registry.batch_operation')->shouldNotBeCalled();

        $compilerPass->process($container->reveal());
    }
}
