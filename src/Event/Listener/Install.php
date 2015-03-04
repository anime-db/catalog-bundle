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
use AnimeDb\Bundle\CatalogBundle\Event\Install\App;

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
     * Construct
     *
     * @param \AnimeDb\Bundle\AnimeDbBundle\Manipulator\Parameters $manipulator
     * @param \AnimeDb\Bundle\AppBundle\Service\CacheClearer $cache_clearer
     */
    public function __construct(Parameters $manipulator, Parameters $cache_clearer)
    {
        $this->manipulator = $manipulator;
        $this->cache_clearer = $cache_clearer;
    }

    /**
     * On install package
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Event\Install\App $event
     */
    public function onInstallApp(App $event)
    {
        // update param
        $this->manipulator->set('anime_db.catalog.installed', true);
        $this->cache_clearer->clear();
    }
}
