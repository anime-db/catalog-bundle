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

use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Parameters;
use AnimeDb\Bundle\AppBundle\Service\CacheClearer;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Translation\TranslatorInterface;
use AnimeDb\Bundle\CatalogBundle\Event\Install\Samples as SamplesInstall;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use AnimeDb\Bundle\CatalogBundle\Entity\Label;
use AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item;
use AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item\Chain;

/**
 * Install listener
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Listener
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Install
{
    /**
     * Parameters manipulator
     *
     * @var \AnimeDb\Bundle\AnimeDbBundle\Manipulator\Parameters
     */
    protected $manipulator;

    /**
     * Cache clearer
     *
     * @var \AnimeDb\Bundle\AppBundle\Service\CacheClearer
     */
    protected $cache_clearer;

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
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * Item chain
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item\Chain
     */
    protected $item_chain;

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
     * Construct
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Manipulator\Parameters $manipulator
     * @param \AnimeDb\Bundle\AppBundle\Service\CacheClearer $cache_clearer
     * @param \AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item\Chain $item_chain
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     * @param \Symfony\Component\Filesystem\Filesystem $fs
     * @param \Symfony\Component\HttpKernel\KernelInterface $kernel
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     * @param string $root_dir
     * @param boolean $installed
     */
    public function __construct(
        Parameters $manipulator,
        CacheClearer $cache_clearer,
        Chain $item_chain,
        ObjectManager $em,
        Filesystem $fs,
        KernelInterface $kernel,
        TranslatorInterface $translator,
        $root_dir,
        $installed
    ) {
        $this->em = $em;
        $this->fs = $fs;
        $this->kernel = $kernel;
        $this->translator = $translator;
        $this->target_dir = $root_dir.'/../web/media/';
        $this->installed = $installed;
        $this->item_chain = $item_chain;
        $this->manipulator = $manipulator;
        $this->cache_clearer = $cache_clearer;
    }

    /**
     * On install application
     */
    public function onInstallApp()
    {
        // update param
        $this->manipulator->set('anime_db.catalog.installed', true);
        $this->cache_clearer->clear();

        // install labels
        $this->em->persist((new Label())->setName($this->translator->trans('Scheduled')));
        $this->em->persist((new Label())->setName($this->translator->trans('Watching')));
        $this->em->persist((new Label())->setName($this->translator->trans('Views')));
        $this->em->persist((new Label())->setName($this->translator->trans('Postponed')));
        $this->em->persist((new Label())->setName($this->translator->trans('Dropped')));
        $this->em->flush();
    }

    /**
     * On install samples
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Event\Install\Samples $event
     */
    public function onInstallSamples(SamplesInstall $event)
    {
        // app already installed
        if ($this->installed) {
            return;
        }

        // sample label
        $name = $this->translator->trans('Sample');
        $label = $this->em->getRepository('AnimeDbCatalogBundle:Label')->findOneBy(['name' => $name]);
        $label = $label ?: (new Label())->setName($name);

        $status = false;
        // create items
        foreach ($this->item_chain->getPublicItems() as $item) {
            $status = $this->persist($item, $event->getStorage(), $label) ?: $status;
        }

        // install more items only for debug mode
        if ($this->kernel->isDebug()) {
            foreach ($this->item_chain->getDebugItems() as $item) {
                $status = $this->persist($item, $event->getStorage(), $label) ?: $status;
            }
        }

        if ($status) {
            $this->em->flush();
        }
    }

    /**
     * Persist item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item $item
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Label $label
     */
    protected function persist(Item $item, Storage $storage, Label $label)
    {
        if (!$this->fs->exists($this->getTargetCover($item))) {
            $this->em->persist($item
                ->setStorage($storage)
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