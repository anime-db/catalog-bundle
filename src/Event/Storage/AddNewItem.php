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
use AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\FillerInterface;

/**
 * Event thrown when a new item is added
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Storage
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class AddNewItem extends Event
{
    /**
     * @var ArrayCollection
     */
    protected $fillers;

    /**
     * @var Item
     */
    protected $item;

    /**
     * @param Item $item
     * @param FillerInterface $filler
     */
    public function __construct(Item $item, FillerInterface $filler)
    {
        $this->item = $item;
        $this->fillers = new ArrayCollection([$filler]);
    }

    /**
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @return ArrayCollection
     */
    public function getFillers()
    {
        return $this->fillers;
    }

    /**
     * @param FillerInterface $filler
     *
     * @return AddNewItem
     */
    public function addFiller(FillerInterface $filler)
    {
        if (!$this->fillers->contains($filler)) {
            $this->fillers->add($filler);
        }
        return $this;
    }
}
