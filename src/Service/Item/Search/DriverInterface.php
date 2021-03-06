<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Service\Item\Search;

use AnimeDb\Bundle\CatalogBundle\Entity\Search;

/**
 * Item search driver interface.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
interface DriverInterface
{
    /**
     * Search items.
     *
     * @param Search $data
     * @param int $limit
     * @param int $offset
     * @param string $sort_column
     * @param string $sort_direction
     *
     * @return array {list:[],total:0}
     */
    public function search(Search $data, $limit, $offset, $sort_column, $sort_direction);

    /**
     * Search items by name.
     *
     * @param string $name
     * @param int $limit
     *
     * @return array
     */
    public function searchByName($name, $limit = 0);
}
