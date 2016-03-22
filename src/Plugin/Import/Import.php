<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin\Import;

use AnimeDb\Bundle\CatalogBundle\Plugin\Plugin;
use Knp\Menu\ItemInterface;
use Symfony\Component\Form\AbstractType;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;

/**
 * Plugin import
 * 
 * @package AnimeDb\Bundle\CatalogBundle\Plugin\Import
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Import extends Plugin
{
    /**
     * @return AbstractType
     */
    abstract public function getForm();

    /**
     * Import items from source data
     *
     * @param array $data
     *
     * @return Item[]
     */
    abstract public function import(array $data);

    /**
     * Build menu for plugin
     *
     * @param ItemInterface $item
     */
    public function buildMenu(ItemInterface $item)
    {
        $item->addChild($this->getTitle(), [
            'route' => 'item_import',
            'routeParameters' => ['plugin' => $this->getName()]
        ]);
    }
}
