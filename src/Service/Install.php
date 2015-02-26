<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use AnimeDb\Bundle\CatalogBundle\Service\Install\Item;
use AnimeDb\Bundle\CatalogBundle\Service\Install\Item\OnePiece;
use AnimeDb\Bundle\CatalogBundle\Service\Install\Item\FullmetalAlchemist;
use AnimeDb\Bundle\CatalogBundle\Service\Install\Item\SpiritedAway;

/**
 * Installation service
 *
 * @package AnimeDb\Bundle\CatalogBundle\Service
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Install
{
    /**
     * Entity manager
     *
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;

    /**
     * Filesystem
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * Kernel
     *
     * @var \Symfony\Component\HttpKernel\KernelInterface
     */
    protected $kernel;

    /**
     * Root dir
     *
     * @var string
     */
    protected $root_dir = '';

    /**
     * App is installed
     *
     * @var boolean
     */
    protected $installed = false;

    /**
     * Locale
     *
     * @var string
     */
    protected $locale = '';

    /**
     * Construct
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     * @param \Symfony\Component\Filesystem\Filesystem $fs
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     * @param string $root_dir
     * @param boolean $installed
     * @param string $locale
     */
    public function __construct(
        ObjectManager $em,
        Filesystem $fs,
        KernelInterface $kernel,
        $root_dir,
        $installed,
        $locale
    ) {
        $this->em = $em;
        $this->fs = $fs;
        $this->kernel = $kernel;
        $this->root_dir = $root_dir;
        $this->installed = $installed;
        $this->locale = substr($locale, 0, 2);
    }

    /**
     * Install samples
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     */
    public function installSamples(Storage $storage)
    {
        // app already installed
        if ($this->installed) {
            return;
        }

        // copy images for sample items
        $this->fs->mirror(
            $this->kernel->locateResource('@AnimeDbCatalogBundle/Resources/private/images/samples/'),
            $this->root_dir.'/../web/media/samples/'
        );

        // create items
        $this->persistItem(new OnePiece($this->em), $storage);
        $this->persistItem(new FullmetalAlchemist($this->em), $storage);
        $this->persistItem(new SpiritedAway($this->em), $storage);
        $this->em->flush();
    }

    /**
     * Persist iItem
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Service\Install\Item $item
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     */
    protected function persistItem(Item $item, Storage $storage)
    {
        $this->em->persist(
            $item
                ->setStorage($storage)
                ->setLocale($this->locale)
                ->getItem()
        );
    }
}
