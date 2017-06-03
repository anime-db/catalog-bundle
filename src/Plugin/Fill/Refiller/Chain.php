<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Refiller;

use AnimeDb\Bundle\CatalogBundle\Plugin\Chain as ChainPlugin;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;

/**
 * Chain refiller plugins.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Chain extends ChainPlugin
{
    /**
     * Get list of plugins that can fill item.
     *
     * @param Item $item
     * @param string $field
     *
     * @return RefillerInterface[]
     */
    public function getPluginsThatCanFillItem(Item $item, $field)
    {
        $plugins = [];
        /* @var $plugin Refiller */
        foreach ($this->plugins as $plugin) {
            if ($plugin->isCanRefill($item, $field) || $plugin->isCanSearch($item, $field)) {
                $plugins[] = $plugin;
            }
        }

        return $plugins;
    }
}
