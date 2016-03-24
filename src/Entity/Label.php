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
 * Label
 *
 * @ORM\Entity
 * @ORM\Table(name="label")
 * @ORM\Entity(repositoryClass="AnimeDb\Bundle\CatalogBundle\Repository\Label")
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Label
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $id = 0;

    /**
     * @ORM\Column(type="string", length=16)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $name = '';

    /**
     * @ORM\ManyToMany(targetEntity="Item", mappedBy="labels")
     *
     * @var ArrayCollection
     */
    protected $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return Label
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
     * @return Label
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
     * @param Item $item
     *
     * @return Label
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
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
