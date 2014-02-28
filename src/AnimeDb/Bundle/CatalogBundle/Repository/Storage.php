<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage as StorageEntity;

/**
 * Storage repository
 *
 * @package AnimeDb\Bundle\CatalogBundle\Repository
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Storage extends EntityRepository
{
    /**
     * Get storage list for types
     *
     * @param array $types
     *
     * @return array [\AnimeDb\Bundle\CatalogBundle\Entity\Storage]
     */
    public function getList(array $types = array())
    {
        if (!$types || $types == StorageEntity::$type_names) {
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
     * Get last update
     *
     * @param integer|null $id
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
}