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
use Symfony\Component\HttpFoundation\File\File;
use AnimeDb\Bundle\AppBundle\Service\Downloader\Entity\BaseEntity;
use AnimeDb\Bundle\AppBundle\Service\Downloader\Entity\ImageInterface;

/**
 * Item images
 *
 * @ORM\Entity
 * @ORM\Table(name="image")
 * @IgnoreAnnotation("ORM")
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Image extends BaseEntity implements ImageInterface
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
     * Source
     *
     * @ORM\Column(type="string", length=256)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $source;

    /**
     * Items list
     *
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="images", cascade={"persist"})
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
     * Set source
     *
     * @param string $source
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Image
     */
    public function setSource($source)
    {
        $this->setFilename($source);
        return $this;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->getFilename();
    }

    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\AppBundle\Service\Downloader\Entity\BaseEntity::getFilename()
     */
    public function getFilename()
    {
        return $this->source ?: parent::getFilename();
    }

    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\AppBundle\Service\Downloader\Entity\BaseEntity::setFilename()
     */
    public function setFilename($filename)
    {
        $this->source = $filename;
        parent::setFilename($filename);
    }

    /**
     * Set item
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Item $item
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Image
     */
    public function setItem(Item $item = null)
    {
        if ($this->item !== $item) {
            $this->item = $item;
            if ($item instanceof Item) {
                $this->item->addImage($this);
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
     * Rename image if in temp folder
     *
     * @ORM\PrePersist
     */
    public function doRenameImageFile()
    {
        // TODO move it to listeners
        if ($this->source && strpos($this->source, 'tmp') !== false) {
            $filename = pathinfo($this->source, PATHINFO_BASENAME);
            $file = new File($this->getAbsolutePath());
            $this->source = $this->item->getDateAdd()->format('Y/m/d/His/').$filename;
            $file->move(pathinfo($this->getAbsolutePath(), PATHINFO_DIRNAME), $filename);
        }
    }
}
