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
use AnimeDb\Bundle\CatalogBundle\Entity\Item as ItemEntity;

/**
 * @package AnimeDb\Bundle\CatalogBundle\Repository
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Item extends EntityRepository
{
    /**
     * @return int
     */
    public function count()
    {
        return $this->getEntityManager()->createQuery('
            SELECT
                COUNT(i)
            FROM
                AnimeDbCatalogBundle:Item i
        ')->getSingleScalarResult();
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return ItemEntity[]
     */
    public function getList($limit = 0, $offset = 0)
    {
        $query = $this->getEntityManager()->createQuery('
            SELECT
                i
            FROM
                AnimeDbCatalogBundle:Item i
            ORDER BY
                i.id DESC
        ');

        if ($limit) {
            $query
                ->setFirstResult($offset)
                ->setMaxResults($limit);
        }
        return $query->getResult();
    }

    /**
     * @param ItemEntity $item
     *
     * @return array
     */
    public function findDuplicate(ItemEntity $item)
    {
        // get all names
        $names = [$item->getName()];
        foreach ($item->getNames() as $name) {
            $names[] = $name->getName();
        }

        // find from item main name
        $duplicate = $this->getEntityManager()->createQuery('
            SELECT
                i
            FROM
                AnimeDbCatalogBundle:Item i
            WHERE
                i.name IN (:names)
        ')
            ->setParameter(':names', $names)
            ->getResult();

        // find frim item other names
        $item_names = $this->getEntityManager()->createQuery('
            SELECT
                n
            FROM
                AnimeDbCatalogBundle:Name n
            WHERE
                n.name IN (:names)
            GROUP BY
                n.item
        ')
            ->setParameter(':names', $names)
            ->getResult();

        foreach ($item_names as $item_name) {
            // element has been added
            foreach ($duplicate as $item) {
                if ($item === $item_name->getItem()) {
                    continue 2;
                }
            }
            $duplicate[] = $item_name->getItem();
        }

        return $duplicate;
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
                    i.date_update
                FROM
                    AnimeDbCatalogBundle:Item i
                WHERE
                    i.id = :id'
            )
                ->setParameter(':id', $id)
                ->getOneOrNullResult();
        } else {
            $result = $this->getEntityManager()->createQuery('
                SELECT
                    i.date_update
                FROM
                    AnimeDbCatalogBundle:Item i
                ORDER BY
                    i.date_update DESC'
            )
                ->setMaxResults(1)
                ->getOneOrNullResult();
        }

        return $result ? $result['date_update'] : null;
    }
}
