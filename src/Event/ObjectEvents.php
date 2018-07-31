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

interface ObjectEvents
{
    public const PRE_OBJECT_CREATE = 'sauls.object_registry.event.pre_object_create';
    public const POST_OBJECT_CREATE = 'sauls.object_registry.event.post_object_create';
    public const PRE_OBJECT_MODIFY = 'sauls.object_registry.event.pre_object_modify';
    public const POST_OBJECT_MODIFY = 'sauls.object_registry.event.post_object_modify';
}
