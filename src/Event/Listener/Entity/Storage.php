<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Event\Listener\Entity;

use Symfony\Component\Filesystem\Filesystem;
use Doctrine\ORM\Event\LifecycleEventArgs;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage as StorageEntity;

/**
 * Entity storage listener
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Listener
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Storage
{
    /**
     * File name for store the storage id
     *
     * @var string
     */
    const ID_FILE = '.storage';

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @param Filesystem $fs
     */
    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof StorageEntity &&
            $this->fs->exists($entity->getPath()) &&
            !$this->fs->exists($entity->getPath().self::ID_FILE)
        ) {
            $this->fs->dumpFile($entity->getPath().self::ID_FILE, $entity->getId(), 0666);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof StorageEntity &&
            $this->fs->exists($entity->getPath().self::ID_FILE) &&
            (file_get_contents($entity->getPath().self::ID_FILE) == $entity->getId())
        ) {
            $this->fs->remove($entity->getPath().self::ID_FILE);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof StorageEntity) {
            // remove old ids
            foreach ($entity->getOldPaths() as $path) {
                if ($this->fs->exists($path.self::ID_FILE) &&
                    (file_get_contents($path.self::ID_FILE) == $entity->getId())
                ) {
                    $this->fs->remove($path.self::ID_FILE);
                }
            }
            // save storage id
            $this->postPersist($args);
        }
    }
}
