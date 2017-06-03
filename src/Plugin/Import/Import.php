<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Import;

use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;
use Knp\Menu\ItemInterface;

/**
 * Plugin import.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Import extends Plugin implements ImportInterface
{
    /**
     * @param ItemInterface $item
     *
     * @return ItemInterface
     */
    public function buildMenu(ItemInterface $item)
    {
        return $item->addChild($this->getTitle(), [
            'route' => 'item_import',
            'routeParameters' => ['plugin' => $this->getName()],
        ]);
    }
}
