<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Driver;

use AnimeDb\Bundle\CatalogBundle\Entity\Search;
use AnimeDb\Bundle\CatalogBundle\Service\Item\Search\DriverInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector;

/**
 * Search driver use a SQL LIKE for select name
 *
 * @package AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Driver
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class SqlLike implements DriverInterface
{
    /**
     * Item repository
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Repository\Item
     */
    protected $repository;

    /**
     * Selector
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector
     */
    protected $selector;

    /**
     * Construct
     *
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     * @param \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector $selector
     */
    public function __construct(Registry $doctrine, Selector $selector)
    {
        $this->repository = $doctrine->getRepository('AnimeDbCatalogBundle:Item');
        $this->selector = $selector;

        // register custom lower()
        $conn = $doctrine->getConnection()->getWrappedConnection();
        if (method_exists($conn, 'sqliteCreateFunction')) {
            $conn->sqliteCreateFunction('lower', function ($str) {
                return mb_strtolower($str, 'UTF8');
            }, 1);
        }
    }

    /**
     * Search items
     * 
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Search $entity
     * @param integer $limit
     * @param integer $offset
     * @param string $sort_column
     * @param string $sort_direction
     *
     * @return array {list:[],total:0}
     */
    public function search(Search $entity, $limit, $offset, $sort_column, $sort_direction)
    {
        $selector = $this->selector
            ->create()
            ->addCountry($entity)
            ->addDateAdd($entity)
            ->addDateEnd($entity)
            ->addDatePremiere($entity)
            ->addGenres($entity)
            ->addLabels($entity)
            ->addName($entity)
            ->addStorage($entity)
            ->addStudio($entity)
            ->addType($entity)
            ->sort($sort_column, $sort_direction)
            ->limit($limit)
            ->offset($offset);

        return [
            'list' => $selector
                ->getQuerySelect()
                ->getQuery()
                ->getResult(),
            'total' => $selector
                ->getQueryTotal()
                ->getQuery()
                ->getSingleScalarResult()
        ];
    }

    /**
     * Search items by name
     * 
     * @param string $name
     * @param integer $limit
     *
     * @return array
     */
    public function searchByName($name, $limit = 0)
    {
        if (!$name) {
            return [];
        }

        /* @var $selector \Doctrine\ORM\QueryBuilder */
        $selector = $this->repository->createQueryBuilder('i');
        $selector
            ->innerJoin('i.names', 'n')
            ->andWhere('LOWER(i.name) LIKE :name OR LOWER(n.name) LIKE :name')
            ->setParameter('name', preg_replace('/%+/', '%%', mb_strtolower($name, 'UTF8')).'%');

        if ($limit > 0) {
            $selector->setMaxResults($limit);
        }

        // get items
        return $selector
            ->groupBy('i')
            ->getQuery()
            ->getResult();
    }
}
