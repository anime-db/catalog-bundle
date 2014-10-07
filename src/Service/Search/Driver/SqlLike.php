<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Service\Search\Driver;

use AnimeDb\Bundle\CatalogBundle\Entity\Search;
use AnimeDb\Bundle\CatalogBundle\Service\Search\DriverInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use AnimeDb\Bundle\CatalogBundle\Service\Search\Manager;
use AnimeDb\Bundle\CatalogBundle\Entity\Type as TypeEntity;
use AnimeDb\Bundle\CatalogBundle\Entity\Country as CountryEntity;
use AnimeDb\Bundle\CatalogBundle\Entity\Genre as GenreEntity;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage as StorageEntity;
use AnimeDb\Bundle\CatalogBundle\Entity\Studio as StudioEntity;
use AnimeDb\Bundle\CatalogBundle\Entity\Label as LabelEntity;

/**
 * Search driver use a SQL LIKE for select name
 *
 * @package AnimeDb\Bundle\CatalogBundle\Service\Search\Driver
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
     * Construct
     *
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->repository = $doctrine->getRepository('AnimeDbCatalogBundle:Item');

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
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Search $data
     * @param integer $limit
     * @param integer $offset
     * @param string $sort_column
     * @param string $sort_direction
     *
     * @return array {list:[],total:0}
     */
    public function search(Search $data, $limit, $offset, $sort_column, $sort_direction)
    {
        /* @var $selector \Doctrine\ORM\QueryBuilder */
        $selector = $this->repository->createQueryBuilder('i');

        // main name
        if ($data->getName()) {
            $selector
                ->innerJoin('i.names', 'n')
                ->andWhere('LOWER(i.name) LIKE :name OR LOWER(n.name) LIKE :name')
                ->setParameter('name', preg_replace('/%+/', '%%', mb_strtolower($data->getName(), 'UTF8')).'%');
        }
        // date add
        if ($data->getDateAdd() instanceof \DateTime) {
            $selector->andWhere('i.date_add >= :date_add')
                ->setParameter('date_add', $data->getDateAdd()->format('Y-m-d'));
        }
        // date premiere
        if ($data->getDatePremiere() instanceof \DateTime) {
            $selector->andWhere('i.date_premiere >= :date_premiere')
                ->setParameter('date_premiere', $data->getDatePremiere()->format('Y-m-d'));
        }
        // date end
        if ($data->getDateEnd() instanceof \DateTime) {
            $selector->andWhere('i.date_end <= :date_end')
                ->setParameter('date_end', $data->getDateEnd()->format('Y-m-d'));
        }
        // country
        if ($data->getCountry() instanceof CountryEntity) {
            $selector->andWhere('i.country = :country')
                ->setParameter('country', $data->getCountry()->getId());
        }
        // storage
        if ($data->getStorage() instanceof StorageEntity) {
            $selector->andWhere('i.storage = :storage')
                ->setParameter('storage', $data->getStorage()->getId());
        }
        // type
        if ($data->getType() instanceof TypeEntity) {
            $selector->andWhere('i.type = :type')
                ->setParameter('type', $data->getType()->getId());
        }
        // genres
        if ($data->getGenres()->count()) {
            $ids = [];
            foreach ($data->getGenres() as $key => $genre) {
                $ids[] = (int)$genre->getId();
            }
            $selector->innerJoin('i.genres', 'g')
                ->andWhere('g.id IN ('.implode(',', $ids).')');
        }
        // labels
        if ($data->getLabels()->count()) {
            $ids = [];
            foreach ($data->getLabels() as $key => $label) {
                $ids[] = (int)$label->getId();
            }
            $selector->innerJoin('i.labels', 'l')
                ->andWhere('l.id IN ('.implode(',', $ids).')');
        }
        // studio
        if ($data->getStudio() instanceof StudioEntity) {
            $selector->andWhere('i.studio = :studio')
                ->setParameter('studio', $data->getStudio()->getId());
        }

        // get count all items
        $total = clone $selector;
        $total = $total
            ->select('COUNT(DISTINCT i)')
            ->getQuery()
            ->getSingleScalarResult();

        // genres
        if ($data->getGenres()->count()) {
            $selector->andHaving('COUNT(i.id) = '.$data->getGenres()->count());
        }

        // apply order
        $selector->orderBy('i.'.$sort_column, $sort_direction);
        if ($sort_column != 'name') {
            $selector->addOrderBy('i.name', $sort_direction);
        }

        if ($offset) {
            $selector->setFirstResult($offset);
        }
        if ($limit) {
            $selector->setMaxResults($limit);
        }

        // get items
        $list = $selector
            ->groupBy('i.id')
            ->getQuery()
            ->getResult();

        return [
            'list' => $list,
            'total' => $total
        ];
    }

    /**
     * Search items by name
     * 
     * @param string $name
     * @param integer $limit
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
