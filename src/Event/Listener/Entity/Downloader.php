<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Event\Listener\Entity;

use Symfony\Component\Filesystem\Filesystem;
use Doctrine\ORM\Event\LifecycleEventArgs;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use AnimeDb\Bundle\CatalogBundle\Entity\Image;
use AnimeDb\Bundle\AppBundle\Service\Downloader\Entity\EntityInterface;

/**
 * Entity downloader listener.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Downloader
{
    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var string
     */
    protected $root = '';

    /**
     * @param Filesystem $fs
     * @param string $root
     */
    public function __construct(Filesystem $fs, $root)
    {
        $this->fs = $fs;
        $this->root = $root;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Item) {
            $this->renameFile($entity, $entity->getDateAdd()->format('Y/m/d/His/'));
        } elseif ($entity instanceof Image) {
            $this->renameFile($entity, $entity->getItem()->getDateAdd()->format('Y/m/d/His/'));
        }
    }

    /**
     * Rename file, if it in the temp folder.
     *
     * @param EntityInterface $entity
     * @param string $target
     */
    protected function renameFile(EntityInterface $entity, $target)
    {
        if ($entity->getFilename() && strpos($entity->getFilename(), 'tmp') !== false) {
            $filename = $entity->getFilename();
            $entity->setFilename($target.pathinfo($filename, PATHINFO_BASENAME));
            $root = $this->root.$entity->getDownloadPath().'/';
            $this->fs->copy($root.$filename, $root.$entity->getFilename(), true);
        }
    }
}
