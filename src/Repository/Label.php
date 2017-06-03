<?php

/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Repository;

use AnimeDb\Bundle\CatalogBundle\Entity\Item as ItemEntity;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Label extends EntityRepository
{
    /**
     * @param ArrayCollection $new_labels
     */
    public function updateListLabels(ArrayCollection $new_labels)
    {
        $old_label = new ArrayCollection($this->findAll());
        // remove labels
        foreach ($old_label as $label) {
            if (!$new_labels->contains($label)) {
                foreach ($label->getItems() as $item) {
                    /* @var $item ItemEntity */
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
