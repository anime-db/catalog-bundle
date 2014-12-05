<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
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
 * Search selector builder
 *
 * @package AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Builder
{
    /**
     * Query select
     *
     * @var \Doctrine\ORM\QueryBuilder
     */
    protected $select;

    /**
     * Query total
     *
     * @var \Doctrine\ORM\QueryBuilder
     */
    protected $total;

    /**
     * Construct
     *
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
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
     * Get query select
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQuerySelect()
    {
        return $this->select;
    }

    /**
     * Get query total
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryTotal()
    {
        return $this->total;
    }

    /**
     * Add main name
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Search $entity
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector
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
     * Add date add
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Search $entity
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector
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
     * Add date premiere
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Search $entity
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector
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
     * Add date end
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Search $entity
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector
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
     * Add country
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Search $entity
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector
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
     * Add storage
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Search $entity
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector
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
     * Add type
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Search $entity
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector
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
     * Add genres
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Search $entity
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector
     */
    public function addGenres(Search $entity)
    {
        if ($entity->getGenres()->count()) {
            $this->add(function (QueryBuilder $query) use ($entity) {
                $ids = [];
                foreach ($entity->getGenres() as $genre) {
                    $ids[] = (int)$genre->getId();
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
     * Add labels
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Search $entity
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector
     */
    public function addLabels(Search $entity)
    {
        if ($entity->getLabels()->count()) {
            $this->add(function (QueryBuilder $query) use ($entity) {
                $ids = [];
                foreach ($entity->getLabels() as $label) {
                    $ids[] = (int)$label->getId();
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
     * Add studio
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Search $entity
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Service\Item\Search\Selector
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
     * Do add data to queries
     *
     * @param \Closure $adder
     */
    protected function add(\Closure $adder)
    {
        $adder($this->select);
        $adder($this->total);
    }

    /**
     * Set limit
     *
     * @param integer $limit
     */
    public function limit($limit)
    {
        if ($limit > 0) {
            $this->select->setMaxResults($limit);
        }
    }

    /**
     * Set offset
     *
     * @param integer $offset
     */
    public function offset($offset)
    {
        if ($offset > 0) {
            $this->select->setFirstResult($offset);
        }
    }

    /**
     * Sort
     *
     * @param string $column
     * @param string $direction
     */
    public function sort($column, $direction)
    {
        $this->select->orderBy('i.'.$column, $direction);
    }
}
