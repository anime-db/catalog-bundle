<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Event\Storage;

use Symfony\Component\EventDispatcher\Event;
use Doctrine\Common\Collections\ArrayCollection;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler;

/**
 * Event thrown when a new item is added
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Storage
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class AddNewItem extends Event
{

    /**
     * Fillers
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $fillers;

    /**
     * Item
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    protected $item;

    /**
     * Construct
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     * @param \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler $filler
     */
    public function __construct(Item $item, Filler $filler)
    {
        $this->item = $item;
        $this->fillers = new ArrayCollection([$filler]);
    }

    /**
     * Get item
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Get fillers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getFillers()
    {
        return $this->fillers;
    }

    /**
     * Add filler
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler $filler
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Event\Storage\AddNewItem
     */
    public function addFiller(Filler $filler)
    {
        if (!$this->fillers->contains($filler)) {
            $this->fillers->add($filler);
        }
        return $this;
    }
}
