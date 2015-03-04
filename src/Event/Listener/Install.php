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
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use AnimeDb\Bundle\CatalogBundle\Event\Install\App;
use AnimeDb\Bundle\CatalogBundle\Entity\Label;

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
     * Translator
     *
     * @var \Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    protected $translator;

    /**
     * Construct
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Manipulator\Parameters $manipulator
     * @param \AnimeDb\Bundle\AppBundle\Service\CacheClearer $cache_clearer
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     * @param \Symfony\Bundle\FrameworkBundle\Translation\Translator $translator
     */
    public function __construct(
        Parameters $manipulator,
        CacheClearer $cache_clearer,
        ObjectManager $em,
        Translator $translator
    ) {
        $this->em = $em;
        $this->translator = $translator;
        $this->manipulator = $manipulator;
        $this->cache_clearer = $cache_clearer;
    }

    /**
     * On install application
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Event\Install\App $event
     */
    public function onInstallApp(App $event)
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
}
