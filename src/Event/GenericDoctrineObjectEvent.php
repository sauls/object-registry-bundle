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

use Doctrine\Common\EventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;

class GenericDoctrineObjectEvent extends GenericObjectEvent
{
    /**
     * @var EventArgs
     */
    private $parent;

    /**
     * @var array
     */
    private $changeSet;

    public function __construct(object $subject = null, EventArgs $parent, array $arguments = array())
    {
        parent::__construct($subject, $arguments);

        $this->parent = $parent;
    }

    public function getEntity(): object
    {
        return $this->getSubject();
    }

    public function getParent(): EventArgs
    {
        return $this->parent;
    }

    public function setParent(EventArgs $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * Get the entire changeset (or just chengeset of an attribute if key is setted).
     *
     * @param string|null $key
     *
     * @return array
     */
    public function getChangeSet($key = null)
    {
        $changeSet = $this->registerChangeSet();
        if (null === $key) {
            return $changeSet;
        }
        if (false === array_key_exists($key, $changeSet)) {
            return false;
        }
        return $changeSet[$key];
    }

    /**
     * @return array
     */
    private function registerChangeSet()
    {
        return $this->changeSet = null === $this->changeSet
            ? $this->getUnitOfWork()->getEntityChangeSet($this->subject)
            : $this->changeSet;
    }

    /**
     * @return UnitOfWork
     */
    public function getUnitOfWork()
    {
        return $this->getEntityManager()->getUnitOfWork();
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        if (\method_exists($this->parent, 'getEntityManager')) {
            return $this->parent->getEntityManager();
        }

        if (\method_exists($this->parent, 'getObjectManager')) {
            return $this->parent->getObjectManager();
        }

        throw new \RuntimeException(sprintf(
                'Method `getEntityManager` or `getObjectManager` does not exist on parent event class `%s`',
                \get_class($this->parent))
        );
    }
}
