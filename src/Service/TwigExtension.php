<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Service;

/**
 * Twig extension.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class TwigExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            'dummy' => new \Twig_Filter_Method($this, 'dummy'),
        ];
    }

    /**
     * Dummy for images.
     *
     * @param string $path
     * @param string $filter
     *
     * @return bool
     */
    public function dummy($path, $filter)
    {
        return $path ?: '/bundles/animedbcatalog/images/dummy/'.$filter.'.jpg';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'extension';
    }
}
