<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * Anime type.
 *
 * @ORM\Entity
 * @ORM\Table(name="type")
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Type implements Translatable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=16)
     *
     * @var string
     */
    protected $id = '';

    /**
     * @ORM\Column(type="string", length=32)
     * @Assert\NotBlank()
     * @Gedmo\Translatable
     *
     * @var string
     */
    protected $name = '';

    /**
     * @ORM\OneToMany(targetEntity="Item", mappedBy="type")
     *
     * @var ArrayCollection
     */
    protected $items;

    /**
     * Entity locale.
     *
     * @Gedmo\Locale
     *
     * @var string
     */
    protected $locale = '';

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @param string $id
     *
     * @return Type
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

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
     * @param Item $item
     *
     * @return Type
     */
    public function addItem(Item $item)
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setType($this);
        }

        return $this;
    }

    /**
     * @param Item $item
     *
     * @return Type
     */
    public function removeItem(Item $item)
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            $item->setType(null);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param string $locale
     *
     * @return Type
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getTranslatableLocale()
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
