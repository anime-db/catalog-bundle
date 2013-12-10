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

use Doctrine\ORM\EntityManager;
use AnimeDb\Bundle\AnimeDbBundle\Event\Project\Updated as UpdatedEvent;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Project listener
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Listener
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Project
{
    /**
     * Root directory
     *
     * @var string
     */
    protected $root;

    /**
     * Construct
     *
     * @param string $kernal_root
     */
    public function __construct($kernal_root)
    {
        $this->root = $kernal_root.'/../';
    }

    /**
     * Update next run date for the propose update task
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Event\Project\Updated $event
     */
    public function onInstalled(UpdatedEvent $event)
    {
        // move example images
        $fs = new Filesystem();
        $fs->copy(__DIR__.'/../../Resources/public/images/example', $this->root.'web/media/');
    }
}