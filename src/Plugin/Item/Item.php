<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Item;

use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;
use AnimeDb\Bundle\CatalogBundle\Plugin\PluginInterface;
use Knp\Menu\ItemInterface;
use AnimeDb\Bundle\CatalogBundle\Entity\Item as ItemEntity;

/**
 * Plugin item interface
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Item
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Item extends Plugin implements PluginInterface
{
    /**
     * @param ItemInterface $node
     * @param ItemEntity $item
     *
     * @return ItemInterface
     */
    abstract public function buildMenu(ItemInterface $node, ItemEntity $item);
}
