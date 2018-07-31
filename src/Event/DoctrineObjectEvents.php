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

namespace Sauls\Bundle\ObjectRegistryBundle\Event;

interface DoctrineObjectEvents extends ObjectEvents
{
    public const PRE_PERSIST = 'sauls.object_registry.event.pre_doctrine_object_persist';
    public const POST_PERSIST = 'sauls.object_registry.event.post_doctrine_object_persist';
    public const PRE_UPDATE = 'sauls.object_registry.event.pre_doctrine_object_update';
    public const POST_UPDATE = 'sauls.object_registry.event.post_doctrine_object_update';
    public const PRE_REMOVE = 'sauls.object_registry.event.pre_doctrine_object_remove';
    public const POST_REMOVE = 'sauls.object_registry.event.post_doctrine_object_remove';

    public const PRE_BATCH_SAVE = 'sauls.object_registry.event.pre_doctrine_object_batch_save';
    public const POST_BATCH_SAVE = 'sauls.object_registry.event.post_doctrine_object_batch_save';
    public const PRE_BATCH_REMOVE = 'sauls.object_registry.event.pre_doctrine_object_batch_remove';
    public const POST_BATCH_REMOVE = 'sauls.object_registry.event.post_doctrine_object_batch_remove';
}
