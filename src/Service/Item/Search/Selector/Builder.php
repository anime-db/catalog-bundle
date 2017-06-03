<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector;

use Doctrine\Bundle\DoctrineBundle\Registry;
use AnimeDb\Bundle\CatalogBundle\Entity\Search;
use Doctrine\ORM\QueryBuilder;
use AnimeDb\Bundle\CatalogBundle\Entity\Type;
use AnimeDb\Bundle\CatalogBundle\Entity\Country;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use AnimeDb\Bundle\CatalogBundle\Entity\Studio;

/**
 * Search selector builder.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Builder
{
    /**
     * @var QueryBuilder
     */
    protected $select;

    /**
     * @var QueryBuilder
     */
    protected $total;

    /**
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->select = $doctrine->getRepository('AnimeDbCatalogBundle:Item')
            ->createQueryBuilder('i')
            ->groupBy('i');
        $this->total = $doctrine->getRepository('AnimeDbCatalogBundle:Item')
            ->createQueryBuilder('i')
            ->select('COUNT(DISTINCT i)');
    }

    /**
     * @return QueryBuilder
     */
    public function getQuerySelect()
    {
        return $this->select;
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryTotal()
    {
        return $this->total;
    }

    /**
     * @param Search $entity
     *
     * @return Builder
     */
    public function addName(Search $entity)
    {
        if ($entity->getName()) {
            $name = mb_strtolower($entity->getName(), 'UTF8');
            $this->add(function (QueryBuilder $query) use ($name) {
                $query
                    ->innerJoin('i.names', 'n')
                    ->andWhere('LOWER(i.name) LIKE :name OR LOWER(n.name) LIKE :name')
                    ->setParameter('name', preg_replace('/%+/', '%%', $name).'%');
            });
        }

        return $this;
    }

    /**
     * @param Search $entity
     *
     * @return Builder
     */
    public function addDateAdd(Search $entity)
    {
        if ($entity->getDateAdd() instanceof \DateTime) {
            $this->add(function (QueryBuilder $query) use ($entity) {
                $query
                    ->andWhere('i.date_add >= :date_add')
                    ->setParameter('date_add', $entity->getDateAdd()->format('Y-m-d'));
            });
        }

        return $this;
    }

    /**
     * @param Search $entity
     *
     * @return Builder
     */
    public function addDatePremiere(Search $entity)
    {
        if ($entity->getDatePremiere() instanceof \DateTime) {
            $this->add(function (QueryBuilder $query) use ($entity) {
                $query
                    ->andWhere('i.date_premiere >= :date_premiere')
                    ->setParameter('date_premiere', $entity->getDatePremiere()->format('Y-m-d'));
            });
        }

        return $this;
    }

    /**
     * @param Search $entity
     *
     * @return Builder
     */
    public function addDateEnd(Search $entity)
    {
        if ($entity->getDateEnd() instanceof \DateTime) {
            $this->add(function (QueryBuilder $query) use ($entity) {
                $query
                    ->andWhere('i.date_end <= :date_end')
                    ->setParameter('date_end', $entity->getDateEnd()->format('Y-m-d'));
            });
        }

        return $this;
    }

    /**
     * @param Search $entity
     *
     * @return Builder
     */
    public function addCountry(Search $entity)
    {
        if ($entity->getCountry() instanceof Country) {
            $this->add(function (QueryBuilder $query) use ($entity) {
                $query
                    ->andWhere('i.country = :country')
                    ->setParameter('country', $entity->getCountry()->getId());
            });
        }

        return $this;
    }

    /**
     * @param Search $entity
     *
     * @return Builder
     */
    public function addStorage(Search $entity)
    {
        if ($entity->getStorage() instanceof Storage) {
            $this->add(function (QueryBuilder $query) use ($entity) {
                $query
                    ->andWhere('i.storage = :storage')
                    ->setParameter('storage', $entity->getStorage()->getId());
            });
        }

        return $this;
    }

    /**
     * @param Search $entity
     *
     * @return Builder
     */
    public function addType(Search $entity)
    {
        if ($entity->getType() instanceof Type) {
            $this->add(function (QueryBuilder $query) use ($entity) {
                $query
                    ->andWhere('i.type = :type')
                    ->setParameter('type', $entity->getType()->getId());
            });
        }

        return $this;
    }

    /**
     * @param Search $entity
     *
     * @return Builder
     */
    public function addGenres(Search $entity)
    {
        if ($entity->getGenres()->count()) {
            $this->add(function (QueryBuilder $query) use ($entity) {
                $ids = [];
                foreach ($entity->getGenres() as $genre) {
                    $ids[] = (int) $genre->getId();
                }
                $query
                    ->innerJoin('i.genres', 'g')
                    ->andWhere('g.id IN ('.implode(',', $ids).')');
            });
            $this->select->andHaving('COUNT(i.id) = '.$entity->getGenres()->count());
        }

        return $this;
    }

    /**
     * Add labels.
     *
     * @param Search $entity
     *
     * @return Builder
     */
    public function addLabels(Search $entity)
    {
        if ($entity->getLabels()->count()) {
            $this->add(function (QueryBuilder $query) use ($entity) {
                $ids = [];
                foreach ($entity->getLabels() as $label) {
                    $ids[] = (int) $label->getId();
                }
                $query
                    ->innerJoin('i.labels', 'l')
                    ->andWhere('l.id IN ('.implode(',', $ids).')');
            });
            $this->select->andHaving('COUNT(i.id) = '.$entity->getLabels()->count());
        }

        return $this;
    }

    /**
     * @param Search $entity
     *
     * @return Builder
     */
    public function addStudio(Search $entity)
    {
        if ($entity->getStudio() instanceof Studio) {
            $this->add(function (QueryBuilder $query) use ($entity) {
                $query
                    ->andWhere('i.studio = :studio')
                    ->setParameter('studio', $entity->getStudio()->getId());
            });
        }

        return $this;
    }

    /**
     * Do add data to queries.
     *
     * @param \Closure $adder
     */
    protected function add(\Closure $adder)
    {
        $adder($this->select);
        $adder($this->total);
    }

    /**
     * @param int $limit
     *
     * @return Builder
     */
    public function limit($limit)
    {
        if ($limit > 0) {
            $this->select->setMaxResults($limit);
        }

        return $this;
    }

    /**
     * @param int $offset
     *
     * @return Builder
     */
    public function offset($offset)
    {
        if ($offset > 0) {
            $this->select->setFirstResult($offset);
        }

        return $this;
    }

    /**
     * @param string $column
     * @param string $direction
     *
     * @return Builder
     */
    public function sort($column, $direction)
    {
        $this->select->orderBy('i.'.$column, $direction);

        return $this;
    }
}
