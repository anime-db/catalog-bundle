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
use AnimeDb\Bundle\CatalogBundle\Entity\Item;

/**
 * Source for item fill
 *
 * @ORM\Entity
 * @ORM\Table(name="source", indexes={
 *   @ORM\Index(name="source_url_idx", columns={"url"})
 * })
 * @IgnoreAnnotation("ORM")
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Source
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
    protected $id = 0;

    /**
     * URL
     *
     * @ORM\Column(type="string", length=256)
     * @Assert\NotBlank()
     * @Assert\Url()
     *
     * @var string
     */
    protected $url = '';

    /**
     * Items list
     *
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="sources", cascade={"persist"})
     * @ORM\JoinColumn(name="item", referencedColumnName="id")
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    protected $item;

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
     * Set url
     *
     * @param string $url
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Source
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Source
     */
    public function setItem(Item $item = null)
    {
        if ($this->item !== $item) {
            // romove link on this item for old item
            if ($this->item instanceof Item) {
                $tmp = $this->item;
                $this->item = null;
                $tmp->removeSource($this);
            }
            $this->item = $item;
            // add link on this item
            if ($item instanceof Item) {
                $this->item->addSource($this);
            }
        }
        return $this;
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
     * To string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getUrl();
    }
}
