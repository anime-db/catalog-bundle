<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage as StorageEntity;

/**
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Storage extends EntityRepository
{
    /**
     * @return int
     */
    public function count()
    {
        return $this->getEntityManager()->createQuery('
            SELECT
                COUNT(s)
            FROM
                AnimeDbCatalogBundle:Storage s
        ')->getSingleScalarResult();
    }

    /**
     * @param array $types
     *
     * @return StorageEntity[]
     */
    public function getList(array $types = [])
    {
        if (!$types || $types == StorageEntity::getTypes()) {
            return $this->getEntityManager()->createQuery('
                SELECT
                    s
                FROM
                    AnimeDbCatalogBundle:Storage s
                ORDER BY
                    s.id DESC
            ')->getResult();
        }

        return $this->getEntityManager()->createQuery('
            SELECT
                s
            FROM
                AnimeDbCatalogBundle:Storage s
            WHERE
                s.type IN (:types)
            ORDER BY
                s.id DESC
        ')
            ->setParameter(':types', $types)
            ->getResult();
    }

    /**
     * @param int|null $id
     *
     * @return \DateTime|null
     */
    public function getLastUpdate($id = null)
    {
        if ($id) {
            $result = $this->getEntityManager()->createQuery('
                SELECT
                    s.date_update
                FROM
                    AnimeDbCatalogBundle:Storage s
                WHERE
                    s.id = :id'
            )
                ->setParameter(':id', $id)
                ->getOneOrNullResult();
        } else {
            $result = $this->getEntityManager()->createQuery('
                SELECT
                    s.date_update
                FROM
                    AnimeDbCatalogBundle:Storage s
                ORDER BY
                    s.date_update DESC'
            )
                ->setMaxResults(1)
                ->getOneOrNullResult();
        }

        return $result ? $result['date_update'] : null;
    }

    /**
     * @return StorageEntity|null
     */
    public function getLast()
    {
        return $this
            ->createQueryBuilder('s')
            ->addOrderBy('s.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
