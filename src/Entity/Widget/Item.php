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

use Doctrine\Common\Collections\ArrayCollection;
use AnimeDb\Bundle\CatalogBundle\Entity\Item as ItemEntity;

/**
 * Widget item
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity\Widget
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Item
{
    /**
     * Cover
     *
     * @var string
     */
    protected $cover = '';

    /**
     * Name
     *
     * @var string
     */
    protected $name = '';

    /**
     * Link to a item in an external catalog
     *
     * @var string
     */
    protected $link = '';

    /**
     * Link for fill item
     *
     * @var string
     */
    protected $link_for_fill = '';

    /**
     * Item in local catalog if have
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item|null
     */
    protected $item;

    /**
     * Type
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Widget\Type
     */
    protected $type;

    /**
     * Genre list
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $genres;

    /**
     * Construct
     */
    public function __construct() {
        $this->genres  = new ArrayCollection();
    }

    /**
     * Set cover
     *
     * @param string $cover
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Widget\Item
     */
    public function setCover($cover)
    {
        $this->cover = $cover;
        return $this;
    }

    /**
     * Get cover
     *
     * @return string
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Widget\Item
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
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Widget\Item
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
     * Set link for fill item
     *
     * @param string $link
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Widget\Item
     */
    public function setLinkForFill($link)
    {
        $this->link_for_fill = $link;
        return $this;
    }

    /**
     * Get link for fill item
     *
     * @return string
     */
    public function getLinkForFill()
    {
        return $this->link_for_fill;
    }

    /**
     * Set item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item|null $item
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Widget\Item
     */
    public function setItem(ItemEntity $item = null)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * Get item
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item|null
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set type
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Widget\Type $type
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Widget\Item
     */
    public function setType(Type $type)
    {
        if ($this->type !== $type) {
            $this->type = $type;
            $type->setItem($this);
        }
        return $this;
    }

    /**
     * Get type
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Widget\Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add genres
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Widget\Genre $genre
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Widget\Item
     */
    public function addGenre(Genre $genre)
    {
        if (!$this->genres->contains($genre)) {
            $this->genres->add($genre);
            $genre->setItem($this);
        }
        return $this;
    }

    /**
     * Get genres
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGenres()
    {
        return $this->genres;
    }
}
