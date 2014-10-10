<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Service;

use AnimeDb\Bundle\CatalogBundle\Repository\Item;
use AnimeDb\Bundle\CatalogBundle\Service\Search\Manager;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * List items service
 *
 * @package AnimeDb\Bundle\CatalogBundle\Service
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ListItems
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
     * Sort items by field
     *
     * @var array
     */
    public static $sort_by_field = [
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
     * Repository
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Repository\Item
     */
    protected $repository;

    /**
     * Item searcher
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Service\Search\Manager
     */
    protected $searcher;

    /**
     * Construct
     *
     * @param \Doctrine\Common\Persistence\ManagerRegistry $doctrine
     * @param \AnimeDb\Bundle\CatalogBundle\Service\Search\Manager $searcher
     */
    public function __construct(ManagerRegistry $doctrine, Manager $searcher)
    {
        $this->repository = $doctrine->getRepository('AnimeDbCatalogBundle:Item');
        $this->searcher = $searcher;
    }

    /**
     * Get limit list items
     *
     * @param integer|null $limit
     *
     * @return integer
     */
    public function getLimit($limit = null)
    {
        if (!is_numeric($limit)) {
            return self::DEFAULT_LIMIT;
        }
        return in_array($limit, self::$limits) ? (int)$limit : self::DEFAULT_LIMIT;
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
        $current_limit = $this->getLimit(isset($query['limit']) ? $query['limit'] : null);

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
     * Get total items
     *
     * @return integer
     */
    public function getTotal()
    {
        return $this->repository->count();
    }

    /**
     * Get items list
     *
     * @param integer|null $limit
     * @param integer|null $offset
     *
     * @return array [\AnimeDb\Bundle\CatalogBundle\Entity\Item]
     */
    public function getList($limit = 0, $offset = 0)
    {
        return $this->repository->getList($limit, $offset);
    }

    /**
     * Get sort field
     *
     * @param array $query
     *
     * @return string
     */
    public function getSortField(array $query = [])
    {
        return $this->searcher->getValidSortColumn(isset($query['sort_by']) ? $query['sort_by'] : null);
    }

    /**
     * Get list sorts
     *
     * @param array $query
     *
     * @return array
     */
    public function getSortFields(array $query = [])
    {
        $current_sort_by = $this->getSortField($query);

        // sort by
        $sort_by = [];
        foreach (self::$sort_by_field as $field => $info) {
            $sort_by[] = [
                'name' => $info['name'],
                'title' => $info['title'],
                'current' => $current_sort_by == $field,
                'link' => '?'.http_build_query(
                    array_merge($request->query->all(), ['sort_by' => $field])
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
        return '?'.http_build_query(
            array_merge($query, ['sort_direction' => $this->getSortDirection($query)])
        );
    }
}