<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Event\Install;

use Symfony\Component\EventDispatcher\Event;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;

/**
 * Event thrown when a install samples
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Install
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Samples extends Event
{
    /**
     * Storage
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Storage
     */
    protected $storage;

    /**
     * Construct
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Get storage
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }
}
