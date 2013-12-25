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
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Studio
 *
 * @ORM\Entity
 * @ORM\Table(name="studio")
 * @IgnoreAnnotation("ORM")
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Studio
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
     * Studio name
     *
     * @ORM\Column(type="string", length=128)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $name;

    /**
     * Unified studio name
     *
     * @ORM\Column(type="string", length=128)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $unified_name;

    /**
     * Items list
     *
     * @ORM\OneToMany(targetEntity="Item", mappedBy="studio")
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
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Studio
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
     * Set unified name
     *
     * @param string $unified_name
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Studio
     */
    public function setUnifiedName($unified_name)
    {
        $this->unified_name = $unified_name;
        return $this;
    }

    /**
     * Get unified name
     *
     * @return string
     */
    public function getUnifiedName()
    {
        return $this->unified_name;
    }

    /**
     * Add item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Studio
     */
    public function addItem(\AnimeDb\Bundle\CatalogBundle\Entity\Item $item)
    {
        $this->items[] = $item->setStudio($this);
        return $this;
    }

    /**
     * Remove item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     */
    public function removeItem(\AnimeDb\Bundle\CatalogBundle\Entity\Item $item)
    {
        $this->items->removeElement($item);
        $item->setStudio(null);
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
}