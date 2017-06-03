<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Plugin;

/**
 * Interface PluginInterface.
 */
interface PluginInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getTitle();
}
