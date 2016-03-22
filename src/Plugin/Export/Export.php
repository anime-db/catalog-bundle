<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Export;

use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;
use AnimeDb\Bundle\CatalogBundle\Plugin\PluginInMenuInterface;
use Knp\Menu\ItemInterface;

/**
 * Plugin export
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Export
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Export extends Plugin implements PluginInMenuInterface
{
    /**
     * @param ItemInterface $item
     *
     * @return ItemInterface
     */
    abstract public function buildMenu(ItemInterface $item);
}
