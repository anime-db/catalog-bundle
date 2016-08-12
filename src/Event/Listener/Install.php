<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Event\Listener;

use AnimeDb\Bundle\AnimeDbBundle\Manipulator\Parameters;
use AnimeDb\Bundle\AppBundle\Service\CacheClearer;
use AnimeDb\Bundle\CatalogBundle\Event\Install\Samples;
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
 * Install listener.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Install
{
    /**
     * @var Parameters
     */
    protected $manipulator;

    /**
     * @var CacheClearer
     */
    protected $cache_clearer;

    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var Chain
     */
    protected $item_chain;

    /**
     * @var string
     */
    protected $origin_dir = '';

    /**
     * @var string
     */
    protected $target_dir = '';

    /**
     * @var bool
     */
    protected $installed = false;

    /**
     * @var array
     */
    protected $labels = [
        'Scheduled',
        'Watching',
        'Views',
        'Postponed',
        'Dropped',
    ];

    /**
     * @param Parameters $manipulator
     * @param CacheClearer $cache_clearer
     * @param Chain $item_chain
     * @param ObjectManager $em
     * @param Filesystem $fs
     * @param KernelInterface $kernel
     * @param TranslatorInterface $translator
     * @param string $root_dir
     * @param bool $installed
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

    public function onInstallApp()
    {
        // update param
        $this->manipulator->set('anime_db.catalog.installed', true);
        $this->cache_clearer->clear();

        // prepare labels
        foreach ($this->labels as $key => $label) {
            $this->labels[$key] = $this->translator->trans($label);
        }

        // remove exists labels
        /* @var $labels Label[] */
        $labels = $this->em->getRepository('AnimeDbCatalogBundle:Label')
            ->findBy(['name' => $this->labels]);
        foreach ($labels as $label) {
            $i = array_search($label->getName(), $this->labels);
            if ($i !== false) {
                unset($this->labels[$i]);
            }
        }
        unset($labels);

        // install new labels
        foreach ($this->labels as $label) {
            $this->em->persist((new Label())->setName($label));
        }

        $this->em->flush();
    }

    /**
     * @param Samples $event
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
     * @param Item $item
     * @param Storage $storage
     * @param Label $label
     *
     * @return bool
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
     * @param Item $item
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
     * @param Item $item
     *
     * @return string
     */
    protected function getTargetCover(Item $item)
    {
        return $this->target_dir.$item->getItem()->getCover();
    }
}
