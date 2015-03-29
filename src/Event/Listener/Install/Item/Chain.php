<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item;

use AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item;

/**
 * Chain items
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Chain
{
    /**
     * List debug items
     *
     * @var array
     */
    protected $debug_items = [];

    /**
     * List public items
     *
     * @var array
     */
    protected $public_items = [];

    /**
     * Add public item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item $item
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item\Chain
     */
    public function addPublicItem(Item $item)
    {
        $this->public_items[] = $item;
        return $this;
    }

    /**
     * Add debug item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item $item
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item\Chain
     */
    public function addDebugItem(Item $item)
    {
        $this->debug_items[] = $item;
        return $this;
    }

    /**
     * Get public items
     *
     * @return array [\AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item]
     */
    public function getPublicItems()
    {
        return $this->public_items;
    }

    /**
     * Get debug items
     *
     * @return array [\AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item]
     */
    public function getDebugItems()
    {
        return $this->debug_items;
    }
}
