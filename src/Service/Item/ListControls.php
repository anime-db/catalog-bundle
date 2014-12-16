<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Service\Item;

use AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Manager;

/**
 * Item list controls service
 *
 * @package AnimeDb\Bundle\CatalogBundle\Service\Item
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ListControls
{
    /**
     * Default limit
     *
     * @var integer
     */
    const DEFAULT_LIMIT = 8;

    /**
     * Limit for show all items
     *
     * @var integer
     */
    const LIMIT_ALL = 0;

    /**
     * Limit name for show all items
     *
     * @var integer
     */
    const LIMIT_ALL_NAME = 'All (%total%)';

    /**
     * Limits on the number of items per page
     *
     * @var array
     */
    public static $limits = [8, 16, 32, self::LIMIT_ALL];

    /**
     * Sort items by column
     *
     * @var array
     */
    public static $sort_by_column = [
        'name'        => [
            'title' => 'Item name',
            'name'  => 'Name'
        ],
        'date_update' => [
            'title' => 'Last updated item',
            'name'  => 'Update'
        ],
        'rating' => [
            'title' => 'Item rating',
            'name'  => 'Rating'
        ],
        'date_premiere'  => [
            'title' => 'Date premiere',
            'name'  => 'Date premiere'
        ],
        'date_end'    => [
            'title' => 'End date of issue',
            'name'  => 'Date end'
        ]
    ];

    /**
     * Item searcher
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Manager
     */
    protected $searcher;

    /**
     * Construct
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Manager $searcher
     */
    public function __construct(Manager $searcher)
    {
        $this->searcher = $searcher;
    }

    /**
     * Get limit list items
     *
     * @param array $query
     *
     * @return integer
     */
    public function getLimit(array $query = [])
    {
        if (isset($query['limit']) && is_numeric($query['limit']) && in_array($query['limit'], self::$limits)) {
            return (int)$query['limit'];
        }
        return self::DEFAULT_LIMIT;
    }

    /**
     * Get list limits
     *
     * @param array $query
     *
     * @return array
     */
    public function getLimits(array $query = [])
    {
        $limits = [];
        $current_limit = $this->getLimit($query);

        foreach (self::$limits as $limit) {
            $limits[] = [
                'link' => '?'.http_build_query(array_merge($query, ['limit' => $limit])),
                'name' => $limit ? $limit : self::LIMIT_ALL_NAME,
                'count' => $limit,
                'current' => $current_limit == $limit
            ];
        }

        return $limits;
    }

    /**
     * Get sort column
     *
     * @param array $query
     *
     * @return string
     */
    public function getSortColumn(array $query = [])
    {
        return $this->searcher->getValidSortColumn(isset($query['sort_by']) ? $query['sort_by'] : null);
    }

    /**
     * Get list sort columns
     *
     * @param array $query
     *
     * @return array
     */
    public function getSortColumns(array $query = [])
    {
        $current_sort_by = $this->getSortColumn($query);

        // sort by
        $sort_by = [];
        foreach (self::$sort_by_column as $column => $info) {
            $sort_by[] = [
                'name' => $info['name'],
                'title' => $info['title'],
                'current' => $current_sort_by == $column,
                'link' => '?'.http_build_query(
                    array_merge($query, ['sort_by' => $column])
                )
            ];
        }
        return $sort_by;
    }

    /**
     * Get sort direction
     *
     * @param array $query
     *
     * @return string
     */
    public function getSortDirection(array $query = [])
    {
        $sort_direction = isset($query['sort_direction']) ? $query['sort_direction'] : null;
        return $this->searcher->getValidSortDirection($sort_direction);
    }

    /**
     * Get sort direction link
     *
     * @param array $query
     *
     * @return string
     */
    public function getSortDirectionLink(array $query = [])
    {
        $direction = $this->getSortDirection($query) == 'ASC' ? 'DESC' : 'ASC';
        return '?'.http_build_query(
            array_merge($query, ['sort_direction' => $direction])
        );
    }
}
