<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Entity\Widget;

use Doctrine\Common\Collections\ArrayCollection;
use AnimeDb\Bundle\CatalogBundle\Entity\Item as ItemEntity;

/**
 * Widget item.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Item
{
    /**
     * @var string
     */
    protected $cover = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * Link to a item in an external catalog.
     *
     * @var string
     */
    protected $link = '';

    /**
     * @var string
     */
    protected $link_for_fill = '';

    /**
     * Item in local catalog if have.
     *
     * @return Item|null
     */
    protected $item;

    /**
     * @var Type
     */
    protected $type;

    /**
     * @var ArrayCollection
     */
    protected $genres;

    public function __construct()
    {
        $this->genres = new ArrayCollection();
    }

    /**
     * @param string $cover
     *
     * @return Item
     */
    public function setCover($cover)
    {
        $this->cover = $cover;

        return $this;
    }

    /**
     * @return string
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * @param string $name
     *
     * @return Item
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
     * @return Item
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
     * @param string $link
     *
     * @return Item
     */
    public function setLinkForFill($link)
    {
        $this->link_for_fill = $link;

        return $this;
    }

    /**
     * @return string
     */
    public function getLinkForFill()
    {
        return $this->link_for_fill;
    }

    /**
     * @param ItemEntity|null $item
     *
     * @return Item
     */
    public function setItem(ItemEntity $item = null)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item.
     *
     * @return ItemEntity|null
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set type.
     *
     * @param Type $type
     *
     * @return Item
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
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param Genre $genre
     *
     * @return Item
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
     * @return ArrayCollection
     */
    public function getGenres()
    {
        return $this->genres;
    }
}
