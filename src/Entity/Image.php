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
use AnimeDb\Bundle\AppBundle\Service\Downloader\Entity\BaseEntity;
use AnimeDb\Bundle\AppBundle\Service\Downloader\Entity\ImageInterface;

/**
 * Item images.
 *
 * @ORM\Entity
 * @ORM\Table(name="image")
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Image extends BaseEntity implements ImageInterface
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
    protected $source = '';

    /**
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="images", cascade={"persist"})
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
     * @param string $source
     *
     * @return Image
     */
    public function setSource($source)
    {
        $this->setFilename($source);

        return $this;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->getFilename();
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->source ?: parent::getFilename();
    }

    /**
     * @param string $filename
     *
     * @return Image
     */
    public function setFilename($filename)
    {
        $this->source = $filename;
        parent::setFilename($filename);

        return $this;
    }

    /**
     * @param Item $item
     *
     * @return Image
     */
    public function setItem(Item $item = null)
    {
        if ($this->item !== $item) {
            // romove link on this item for old item
            if ($this->item instanceof Item) {
                $tmp = $this->item;
                $this->item = null;
                $tmp->removeImage($this);
            }
            $this->item = $item;
            // add link on this item
            if ($item instanceof Item) {
                $this->item->addImage($this);
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
        return $this->getSource();
    }
}
