<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Plugin\Item;

use AnimeDb\Bundle\CatalogBundle\Plugin\PluginInterface;
use Knp\Menu\ItemInterface as MenuItemInterface;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;

interface ItemInterface extends PluginInterface
{
    /**
     * @param MenuItemInterface $node
     * @param Item $item
     *
     * @return ItemInterface
     */
    public function buildMenu(MenuItemInterface $node, Item $item);
}
