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
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use AnimeDb\Bundle\CatalogBundle\Entity\Label;
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
     * Translator
     *
     * @var \Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    protected $translator;

    /**
     * Origin dir
     *
     * @var string
     */
    protected $origin_dir = '';

    /**
     * Target dir
     *
     * @var string
     */
    protected $target_dir = '';

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
     * @param \Symfony\Bundle\FrameworkBundle\Translation\Translator $translator
     * @param string $root_dir
     * @param boolean $installed
     * @param string $locale
     */
    public function __construct(
        ObjectManager $em,
        Filesystem $fs,
        KernelInterface $kernel,
        Translator $translator,
        $root_dir,
        $installed,
        $locale
    ) {
        $this->em = $em;
        $this->fs = $fs;
        $this->kernel = $kernel;
        $this->translator = $translator;
        $this->target_dir = $root_dir.'/../web/media/';
        $this->installed = $installed;
        $this->locale = $locale;
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

        // sample label
        $name = substr($this->locale, 0, 2) == 'ru' ? 'Пример' : 'Sample';
        $label = $this->em->getRepository('AnimeDbCatalogBundle:Label')->findOneBy(['name' => $name]);
        $label = $label ?: (new Label())->setName($name);

        // create items
        $status = $this->persist(new OnePiece($this->em, $this->translator), $storage, $label);
        $status = $this->persist(new FullmetalAlchemist($this->em, $this->translator), $storage, $label) ?: $status;
        $status = $this->persist(new SpiritedAway($this->em, $this->translator), $storage, $label) ?: $status;
        if ($status) {
            $this->em->flush();
        }
    }

    /**
     * Persist item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Service\Install\Item $item
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Label $label
     */
    protected function persist(Item $item, Storage $storage, Label $label)
    {
        if (!$this->fs->exists($this->getTargetCover($item))) {
            $this->em->persist($item
                ->setStorage($storage)
                ->setLocale($this->locale)
                ->getItem()
                ->addLabel($label)
            );
            $this->fs->copy($this->getOriginCover($item), $this->getTargetCover($item));
            return true;
        }
        return false;
    }

    /**
     * Get origin cover
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Service\Install\Item $item
     *
     * @return string
     */
    protected function getOriginCover(Item $item)
    {
        if (!$this->origin_dir) {
            $this->origin_dir = $this->kernel->locateResource('@AnimeDbCatalogBundle/Resources/private/images/');
        }
        return $this->origin_dir.$item->getItem()->getCover();
    }

    /**
     * Get target cover
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Service\Install\Item $item
     *
     * @return string
     */
    protected function getTargetCover(Item $item)
    {
        return $this->target_dir.$item->getItem()->getCover();
    }
}
