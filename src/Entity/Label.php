<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Label
 *
 * @ORM\Entity
 * @ORM\Table(name="label")
 * @IgnoreAnnotation("ORM")
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Label
{
    /**
     * Id
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    protected $id;

    /**
     * Label name
     *
     * @ORM\Column(type="string", length=16)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $name;

    /**
     * Items list
     *
     * @ORM\ManyToMany(targetEntity="Item", mappedBy="labels")
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $items;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Label
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
     * Add items
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Label
     */
    public function addItem(Item $item)
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->addLabel($this);
        }
        return $this;
    }

    /**
     * Remove items
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Label
     */
    public function removeItem(Item $item)
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            $item->removeLabel($this);
        }
        return $this;
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * To string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
