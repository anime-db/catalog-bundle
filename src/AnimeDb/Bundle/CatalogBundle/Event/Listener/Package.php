<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Event\Listener;

use AnimeDb\Bundle\AnimeDbBundle\Event\Package\Updated;
use AnimeDb\Bundle\AnimeDbBundle\Event\Package\Installed;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Package listener
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Listener
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Package
{
    /**
     * Filesystem
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * Root dir
     *
     * @var string
     */
    protected $root_dir;

    /**
     * Construct
     *
     * @param \Symfony\Component\Filesystem\Filesystem $fs
     * @param string $root_dir
     */
    public function __construct(Filesystem $fs, $root_dir) {
        $this->fs = $fs;
        $this->root_dir = $root_dir;
    }

    /**
     * On update package
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Event\Package\Updated $event
     */
    public function onUpdate(Updated $event)
    {
        if ($event->getPackage() == 'anime-db/catalog-bundle') {
            // TODO update data
        }
    }

    /**
     * On install package
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Event\Package\Installed $event
     */
    public function onInstall(Installed $event)
    {
        if ($event->getPackage() == 'anime-db/catalog-bundle') {
            // TODO update data
        }
    }
}