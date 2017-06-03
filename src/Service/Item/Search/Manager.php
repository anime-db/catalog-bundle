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
 * Item search.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Manager
{
    /**
     * @var string
     */
    const DEFAULT_SORT_COLUMN = 'date_update';

    /**
     * @var string
     */
    const DEFAULT_SORT_DIRECTION = 'DESC';

    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var array
     */
    public static $sort_columns = [
        'name',
        'date_update',
        'rating',
        'date_premiere',
        'date_end',
    ];

    /**
     * @var array
     */
    public static $sort_direction = [
        'DESC',
        'ASC',
    ];

    /**
     * @param DriverInterface $driver
     */
    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @param Search $data
     * @param int|null $limit
     * @param int|null $offset
     * @param string|null $sort_column
     * @param string|null $sort_direction
     *
     * @return array {list:[],total:0}
     */
    public function search(
        Search $data,
        $limit = 0,
        $offset = 0,
        $sort_column = self::DEFAULT_SORT_COLUMN,
        $sort_direction = self::DEFAULT_SORT_DIRECTION
    ) {
        return $this->driver->search(
            $data,
            ($limit > 0 ? (int) $limit : 0),
            ($offset > 0 ? (int) $offset : 0),
            $this->getValidSortColumn($sort_column),
            $this->getValidSortDirection($sort_direction)
        );
    }

    /**
     * @param string $name
     * @param int $limit
     *
     * @return array
     */
    public function searchByName($name, $limit = 0)
    {
        return $this->driver->searchByName($name, $limit);
    }

    /**
     * @param string|null $column
     *
     * @return string
     */
    public function getValidSortColumn($column = self::DEFAULT_SORT_COLUMN)
    {
        return in_array($column, self::$sort_columns) ? $column : self::DEFAULT_SORT_COLUMN;
    }

    /**
     * @param string|null $direction
     *
     * @return string
     */
    public function getValidSortDirection($direction = self::DEFAULT_SORT_DIRECTION)
    {
        return in_array($direction, self::$sort_direction) ? $direction : self::DEFAULT_SORT_DIRECTION;
    }
}
