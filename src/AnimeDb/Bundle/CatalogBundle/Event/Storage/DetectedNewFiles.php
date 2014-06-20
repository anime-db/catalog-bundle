<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Event\Storage;

use Symfony\Component\EventDispatcher\Event;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Event thrown when a new item files is detected
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Storage
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class DetectedNewFiles extends Event
{
    /**
     * Storage
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Storage
     */
    protected $storage;

    /**
     * File
     *
     * @var \Symfony\Component\Finder\SplFileInfo
     */
    protected $file;

    /**
     * Name
     *
     * @var string
     */
    protected $name;

    /**
     * Construct
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     * @param \Symfony\Component\Finder\SplFileInfo $file
     */
    public function __construct(Storage $storage, SplFileInfo $file)
    {
        $this->storage = $storage;
        $this->file = $file;

        // get clean name
        $this->name = $file->getFilename();
        if ($file->isFile()) {
            $this->name = pathinfo($this->name, PATHINFO_FILENAME);
        }
        $this->name = trim(preg_replace('/^([^\[\]\(\)]+).*$/', '$1', $this->name));
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

    /**
     * Get file
     *
     * @return \Symfony\Component\Finder\SplFileInfo
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}