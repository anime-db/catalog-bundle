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

/**
 * Item name
 *
 * @ORM\Entity
 * @ORM\Table(name="name")
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Name
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
     * @ORM\Column(type="string", length=256)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $name = '';

    /**
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="names", cascade={"persist"})
     * @ORM\JoinColumn(name="item", referencedColumnName="id")
     *
     * @var Item
     */
    protected $item;

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
     * @return Name
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
     * @return Name
     */
    public function setItem(Item $item = null)
    {
        if ($this->item !== $item) {
            // romove link on this item for old item
            if ($this->item instanceof Item) {
                $tmp = $this->item;
                $this->item = null;
                $tmp->removeName($this);
            }
            $this->item = $item;
            // add link on this item
            if ($item instanceof Item) {
                $this->item->addName($this);
            }
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

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
