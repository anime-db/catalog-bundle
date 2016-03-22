<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Entity\Widget;

/**
 * Widget item type
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity\Widget
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Type
{
    /**
     * @var string
     */
    protected $name = '';

    /**
     * Link on external catalog
     *
     * @var string
     */
    protected $link = '';

    /**
     * @var Item
     */
    protected $item;

    /**
     * @param string $name
     *
     * @return Type
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $link
     *
     * @return Type
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param Item $item
     *
     * @return Type
     */
    public function setItem(Item $item)
    {
        if ($this->item !== $item) {
            $this->item = $item->setType($this);
        }
        return $this;
    }

    /**
     * @return Item
     */
    public function getItem()
    {
        return $this->item;
    }
}
