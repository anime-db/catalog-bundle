<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Event\Storage;

use Symfony\Component\EventDispatcher\Event;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Event thrown when a new item files is detected.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class DetectedNewFiles extends Event
{
    /**
     * @var Storage
     */
    protected $storage;

    /**
     * @var SplFileInfo
     */
    protected $file;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param Storage $storage
     * @param SplFileInfo $file
     * @param string $name
     */
    public function __construct(Storage $storage, SplFileInfo $file, $name)
    {
        $this->storage = $storage;
        $this->file = $file;
        $this->name = $name;
    }

    /**
     * @return Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @return SplFileInfo
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
