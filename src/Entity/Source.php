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
use Doctrine\Common\Annotations\Annotation;

/**
 * Source for item fill
 *
 * @ORM\Entity
 * @ORM\Table(name="source", indexes={
 *   @ORM\Index(name="source_url_idx", columns={"url"})
 * })
 * @Annotation\IgnoreAnnotation("ORM")
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Source
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
     * @Assert\Url()
     *
     * @var string
     */
    protected $url = '';

    /**
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="sources", cascade={"persist"})
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
     * @param string $url
     *
     * @return Source
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param Item $item
     *
     * @return Source
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
        return $this->getUrl();
    }
}
