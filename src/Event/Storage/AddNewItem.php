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
     * Filler
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler
     */
    protected $filler;

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
        $this->filler = $filler;
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
     * Get filler
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Plugin\Fill\Filler\Filler
     */
    public function getFiller()
    {
        return $this->filler;
    }
}
