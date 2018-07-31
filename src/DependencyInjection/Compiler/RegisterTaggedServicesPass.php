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

use Sauls\Bundle\ObjectRegistryBundle\Collection\BatchOperationCollection;
use Sauls\Bundle\ObjectRegistryBundle\Collection\ObjectManagerCollection;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterTaggedServicesPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {

        $this->processTaggedServices(
            $container, ObjectManagerCollection::class, 'sauls.object_registry.manager'
        );

        $this->processTaggedServices(
            $container, BatchOperationCollection::class,'sauls.object_registry.batch_operation'
        );
    }

    private function processTaggedServices(ContainerBuilder $container, string $definitionClass, string $tag)
    {
        if (false === $container->has($definitionClass)) {
            return;
        }

        $definition = $container->findDefinition($definitionClass);
        $taggedServices = $container->findTaggedServiceIds($tag);

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('set', ['', new Reference($id)]);
        }
    }


}
