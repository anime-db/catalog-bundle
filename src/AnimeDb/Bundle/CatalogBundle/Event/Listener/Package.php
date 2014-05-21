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

/**
 * Package listener
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Listener
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Package
{
    /**
     * Root dir
     *
     * @var string
     */
    protected $root_dir;

    /**
     * Construct
     *
     * @param string $root_dir
     */
    public function __construct($root_dir) {
        $this->root_dir = $root_dir;
    }

    /**
     * On update package
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Event\Package\Updated $event
     */
    public function onUpdate(Updated $event)
    {
        if ($event->getPackage()->getName() == 'anime-db/catalog-bundle') {
            copy(
                __DIR__.'/../../Resources/views/knp_menu.html.twig',
                $this->root_dir.'/Resources/views/knp_menu.html.twig'
            );
        }
    }

    /**
     * On install package
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Event\Package\Installed $event
     */
    public function onInstall(Installed $event)
    {
        if ($event->getPackage()->getName() == 'anime-db/catalog-bundle') {
            copy(
                __DIR__.'/../../Resources/views/knp_menu.html.twig',
                $this->root_dir.'/Resources/views/knp_menu.html.twig'
            );
        }
    }
}