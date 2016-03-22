<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin;

use Knp\Menu\ItemInterface;

/**
 * Interface PluginInMenuInterface
 * @package AnimeDb\Bundle\CatalogBundle\Plugin
 */
interface PluginInMenuInterface extends PluginInterface
{
    /**
     * @param ItemInterface $item
     *
     * @return ItemInterface
     */
    public function buildMenu(ItemInterface $item);
}
