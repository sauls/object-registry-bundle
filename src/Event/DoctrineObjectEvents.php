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
    public const PRE_SAVE = '';
    public const POST_SAVE = '';
    public const PRE_CREATE = '';
    public const POST_CREATE = '';
    public const PRE_UPDATE = '';
    public const POST_UPDATE = '';
    public const PRE_REMOVE = '';
    public const POST_REMOVE = '';
    public const PRE_BATCH_SAVE = '';
    public const POST_BATCH_SAVE = '';
    public const PRE_BATCH_REMOVE = '';
    public const POST_BATCH_REMOVE = '';
}
