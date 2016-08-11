<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Plugin\Import;

use AnimeDb\Bundle\CatalogBundle\Plugin\PluginInMenuInterface;
use Symfony\Component\Form\AbstractType;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;

/**
 * Interface ImportInterface.
 */
interface ImportInterface extends PluginInMenuInterface
{
    /**
     * @return AbstractType
     */
    public function getForm();

    /**
     * Import items from source data.
     *
     * @param array $data
     *
     * @return Item[]
     */
    public function import(array $data);
}
