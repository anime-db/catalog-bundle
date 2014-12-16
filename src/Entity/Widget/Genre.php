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
 * Widget item Genre
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity\Widget
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Genre
{
    /**
     * Name
     *
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
     * Item
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Widget\Item
     */
    protected $item;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Widget\Type
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set link
     *
     * @param string $link
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Widget\Type
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Widget\Item $item
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Widget\Type
     */
    public function setItem(Item $item)
    {
        if ($this->item !== $item) {
            $this->item = $item->addGenre($this);
        }
        return $this;
    }

    /**
     * Get item
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Widget\Item
     */
    public function getItem()
    {
        return $this->item;
    }
}
