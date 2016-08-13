<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Driver;

use AnimeDb\Bundle\CatalogBundle\Entity\Search;
use AnimeDb\Bundle\CatalogBundle\Repository\Item;
use AnimeDb\Bundle\CatalogBundle\Service\Item\Search\DriverInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector;

/**
 * Search driver use a SQL LIKE for select name.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class SqlLike implements DriverInterface
{
    /**
     * @var Item
     */
    protected $repository;

    /**
     * @var Selector
     */
    protected $selector;

    /**
     * @param Registry $doctrine
     * @param Selector $selector
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
     * @param Search $entity
     * @param int $limit
     * @param int $offset
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
                ->getSingleScalarResult(),
        ];
    }

    /**
     * @param string $name
     * @param int $limit
     *
     * @return array
     */
    public function searchByName($name, $limit = 0)
    {
        if (!$name) {
            return [];
        }

        $selector = $this
            ->repository
            ->createQueryBuilder('i')
            ->innerJoin('i.names', 'n')
            ->where('LOWER(i.name) LIKE :name')
            ->orWhere('LOWER(n.name) LIKE :name')
            ->setParameter('name', preg_replace('/%+/', '%%', mb_strtolower($name, 'UTF-8')).'%')
            ->groupBy('i');

        if ($limit > 0) {
            $selector->setMaxResults($limit);
        }

        // get items
        return $selector
            ->getQuery()
            ->getResult();
    }
}
