<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item;

use AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item;

/**
 * Chain items.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Chain
{
    /**
     * @var array
     */
    protected $debug_items = [];

    /**
     * @var array
     */
    protected $public_items = [];

    /**
     * @param Item $item
     *
     * @return Chain
     */
    public function addPublicItem(Item $item)
    {
        $this->public_items[] = $item;

        return $this;
    }

    /**
     * @param Item $item
     *
     * @return Chain
     */
    public function addDebugItem(Item $item)
    {
        $this->debug_items[] = $item;

        return $this;
    }

    /**
     * @return Item[]
     */
    public function getPublicItems()
    {
        return $this->public_items;
    }

    /**
     * @return Item[]
     */
    public function getDebugItems()
    {
        return $this->debug_items;
    }
}
