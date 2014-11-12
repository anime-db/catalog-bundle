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
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Label repository
 *
 * @package AnimeDb\Bundle\CatalogBundle\Repository
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Label extends EntityRepository
{
    /**
     * Search by name
     *
     * @param string $name
     *
     * @return array
     */
    public function searchByName($name)
    {
        // register custom lower()
        $conn = $this->_em->getConnection()->getWrappedConnection();
        if (method_exists($conn, 'sqliteCreateFunction')) {
            $conn->sqliteCreateFunction('lower', function ($str) {
                return mb_strtolower($str, 'UTF8');
            }, 1);
        }

        return $this->_em->createQuery('
            SELECT
                l
            FROM
                AnimeDbCatalogBundle:Label l
            WHERE
                LOWER(l.name) LIKE :name
        ')
            ->setParameter('name', preg_replace('/%+/', '%%', mb_strtolower($name, 'UTF8')).'%')
            ->getResult();
    }

    /**
     * Update list labels
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $new_labels
     */
    public function updateListLabels(ArrayCollection $new_labels)
    {
        $old_label = new ArrayCollection($this->findAll());
        // remove labals
        foreach ($old_label as $label) {
            if (!$new_labels->contains($label)) {
                /* @var $item \AnimeDb\Bundle\CatalogBundle\Entity\Item */
                foreach ($label->getItems() as $item) {
                    $item->removeLabel($label);
                }
                $this->getEntityManager()->remove($label);
            }
        }

        // add new labals
        foreach ($new_labels as $label) {
            if (!$old_label->contains($label)) {
                $this->getEntityManager()->persist($label);
            }
        }
        $this->getEntityManager()->flush();
    }
}
