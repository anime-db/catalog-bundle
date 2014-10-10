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
use AnimeDb\Bundle\CatalogBundle\Entity\Genre;
use AnimeDb\Bundle\CatalogBundle\Entity\Country;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use AnimeDb\Bundle\CatalogBundle\Entity\Type;
use AnimeDb\Bundle\CatalogBundle\Entity\Label;
use Symfony\Component\Validator\ExecutionContextInterface;
use AnimeDb\Bundle\CatalogBundle\Entity\Studio;
use Doctrine\Bundle\DoctrineBundle\Registry;
use AnimeDb\Bundle\AppBundle\Service\Downloader\Entity\BaseEntity;
use AnimeDb\Bundle\AppBundle\Service\Downloader\Entity\ImageInterface;

/**
 * Item
 *
 * @ORM\Entity
 * @ORM\Table(name="item")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="AnimeDb\Bundle\CatalogBundle\Repository\Item")
 * @Assert\Callback(methods={"isPathValid"})
 * @IgnoreAnnotation("ORM")
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Item extends BaseEntity implements ImageInterface
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
     * Main name
     *
     * @ORM\Column(type="string", length=256)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $name = '';

    /**
     * Main name
     *
     * @ORM\OneToMany(targetEntity="Name", mappedBy="item", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $names;

    /**
     * Type
     *
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="items", cascade={"persist"})
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Type
     */
    protected $type;

    /**
     * Date premiere
     *
     * @ORM\Column(type="date")
     * @Assert\Date()
     *
     * @var \DateTime
     */
    protected $date_premiere;

    /**
     * Date end release
     *
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date()
     *
     * @var \DateTime|null
     */
    protected $date_end;

    /**
     * Genre list
     *
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="items", cascade={"persist"})
     * @ORM\JoinTable(name="items_genres")
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $genres;

    /**
     * Label list
     *
     * @ORM\ManyToMany(targetEntity="Label", inversedBy="items", cascade={"persist"})
     * @ORM\JoinTable(name="items_labels")
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $labels;

    /**
     * Country
     *
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="items", cascade={"persist"})
     * @ORM\JoinColumn(name="country", referencedColumnName="id")
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Country
     */
    protected $country;

    /**
     * Duration
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(type="integer", message="The value {{ value }} is not a valid {{ type }}.")
     *
     * @var integer
     */
    protected $duration = 0;

    /**
     * Summary
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $summary = '';

    /**
     * Disk path
     *
     * @ORM\Column(type="string", length=256, nullable=true)
     *
     * @var string
     */
    protected $path = '';

    /**
     * Storage
     *
     * @ORM\ManyToOne(targetEntity="Storage", inversedBy="items", cascade={"persist"})
     * @ORM\JoinColumn(name="storage", referencedColumnName="id")
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Storage
     */
    protected $storage;

    /**
     * Episodes list
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $episodes = '';

    /**
     * Translate (subtitles and voice)
     *
     * @ORM\Column(type="string", length=256, nullable=true)
     *
     * @var string
     */
    protected $translate = '';

    /**
     * File info
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $file_info = '';

    /**
     * Source list
     *
     * @ORM\OneToMany(targetEntity="Source", mappedBy="item", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $sources;

    /**
     * Cover
     *
     * @ORM\Column(type="string", length=256, nullable=true)
     *
     * @var string
     */
    protected $cover = '';

    /**
     * Number of episodes
     *
     * @ORM\Column(type="string", length=5, nullable=true)
     * @Assert\Regex(
     *     pattern="/^(\d{1,4}\+?)$/",
     *     message="The number of episodes should be a number and can contain a '+' to denote the continuation of production"
     * )
     *
     * @var string
     */
    protected $episodes_number = '';

    /**
     * Date add item
     *
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $date_add;

    /**
     * Date last update item
     *
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $date_update;

    /**
     * Image list
     *
     * @ORM\OneToMany(targetEntity="Image", mappedBy="item", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $images;

    /**
     * Rating
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(type="integer", message="The value {{ value }} is not a valid {{ type }}.")
     *
     * @var integer
     */
    protected $rating = 0;

    /**
     * Studio
     *
     * @ORM\ManyToOne(targetEntity="Studio", inversedBy="items", cascade={"persist"})
     * @ORM\JoinColumn(name="studio", referencedColumnName="id")
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Studio
     */
    protected $studio;

    /**
     * Not cleared path
     *
     * @var string
     */
    protected $not_cleared_path = '';

    /**
     * Construct
     */
    public function __construct() {
        $this->genres = new ArrayCollection();
        $this->labels = new ArrayCollection();
        $this->names = new ArrayCollection();
        $this->sources = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->date_add = new \DateTime();
        $this->date_update = new \DateTime();
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
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
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
     * Set date premiere
     *
     * @param \DateTime|null $date_premiere
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function setDatePremiere(\DateTime $date_premiere = null)
    {
        $this->date_premiere = $date_premiere ? clone $date_premiere : $date_premiere;
        return $this;
    }

    /**
     * Get date premiere
     *
     * @return \DateTime
     */
    public function getDatePremiere()
    {
        return $this->date_premiere ? clone $this->date_premiere : null;
    }

    /**
     * Set date end
     *
     * @param \DateTime|null $date_end
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function setDateEnd(\DateTime $date_end = null)
    {
        $this->date_end = $date_end ? clone $date_end : null;
        return $this;
    }

    /**
     * Get date end
     *
     * @return \DateTime|null
     */
    public function getDateEnd()
    {
        return $this->date_end ? clone $this->date_end : null;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set summary
     *
     * @param string $summary
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function setPath($path)
    {
        if ($path) {
            $this->not_cleared_path = $path;
            $this->doClearPath();
        } else {
            $this->path = $path;
        }
        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        $this->doClearPath();
        // path not cleared
        if ($this->not_cleared_path) {
            return $this->not_cleared_path;
        }
        // use storage path as prefix
        if ($this->getStorage() instanceof Storage && $this->getStorage()->getPath()) {
            return $this->getStorage()->getPath().$this->path;
        }
        return $this->path;
    }

    /**
     * Set episodes
     *
     * @param string $episodes
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function setEpisodes($episodes)
    {
        $this->episodes = $episodes;
        return $this;
    }

    /**
     * Get episodes
     *
     * @return string
     */
    public function getEpisodes()
    {
        return $this->episodes;
    }

    /**
     * Set translate
     *
     * @param string $translate
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function setTranslate($translate)
    {
        $this->translate = $translate;
        return $this;
    }

    /**
     * Get translate
     *
     * @return string
     */
    public function getTranslate()
    {
        return $this->translate;
    }

    /**
     * Set file info
     *
     * @param string $fileInfo
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function setFileInfo($fileInfo)
    {
        $this->file_info = $fileInfo;
        return $this;
    }

    /**
     * Get file_info
     *
     * @return string
     */
    public function getFileInfo()
    {
        return $this->file_info;
    }

    /**
     * Add name
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Name $name
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function addName(Name $name)
    {
        if (!$this->names->contains($name)) {
            $this->names->add($name);
            $name->setItem($this);
        }
        return $this;
    }

    /**
     * Remove name
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Name $name
     */
    public function removeName(Name $name)
    {
        if ($this->names->contains($name)) {
            $this->names->removeElement($name);
            $name->setItem(null);
        }
    }

    /**
     * Get names
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * Set type
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Type $type
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function setType(Type $type = null)
    {
        if ($this->type !== $type) {
            // romove link on this item for old type
            if ($this->type instanceof Type) {
                $tmp = $this->type;
                $this->type = null;
                $tmp->removeItem($this);
            }
            $this->type = $type;
            // add link on this item
            if ($this->type instanceof Type) {
                $this->type->addItem($this);
            }
        }
        return $this;
    }

    /**
     * Get type
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add genre
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Genre $genre
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function addGenre(Genre $genre)
    {
        if (!$this->genres->contains($genre)) {
            $this->genres->add($genre);
            $genre->addItem($this);
        }
        return $this;
    }

    /**
     * Remove genre
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Genre $genre
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function removeGenre(Genre $genre)
    {
        if ($this->genres->contains($genre)) {
            $this->genres->removeElement($genre);
            $genre->removeItem($this);
        }
        return $this;
    }

    /**
     * Get genres
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * Add label
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Label $label
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function addLabel(Label $label)
    {
        if (!$this->labels->contains($label)) {
            $this->labels->add($label);
            $label->addItem($this);
        }
        return $this;
    }

    /**
     * Remove label
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Label $label
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function removeLabel(Label $label)
    {
        if ($this->labels->contains($label)) {
            $this->labels->removeElement($label);
            $label->removeItem($this);
        }
        return $this;
    }

    /**
     * Get labels
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * Set country
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Country $country
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function setCountry(Country $country = null)
    {
        if ($this->country !== $country) {
            // romove link on this item for old country
            if ($this->country instanceof Country) {
                $tmp = $this->country;
                $this->country = null;
                $tmp->removeItem($this);
            }
            $this->country = $country;
            // add link on this item
            if ($this->country instanceof Country) {
                $this->country->addItem($this);
            }
        }
        return $this;
    }

    /**
     * Get country
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set storage
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function setStorage(Storage $storage = null)
    {
        if ($this->storage !== $storage) {
            // romove link on this item for old storage
            if ($this->storage instanceof Storage) {
                $tmp = $this->storage;
                $this->storage = null;
                $tmp->removeItem($this);
            }
            $this->storage = $storage;
            // add link on this item
            if ($this->storage instanceof Storage) {
                $this->storage->addItem($this);
            }
        }
        $this->doClearPath();
        return $this;
    }

    /**
     * Get storage
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Set cover
     *
     * @param string $cover
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function setCover($cover)
    {
        $this->setFilename($cover);
        return $this;
    }

    /**
     * Get cover
     *
     * @return string
     */
    public function getCover()
    {
        return $this->getFilename();
    }

    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\AppBundle\Service\Downloader\Entity\BaseEntity::getFilename()
     */
    public function getFilename()
    {
        return $this->cover ?: parent::getFilename();
    }

    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\AppBundle\Service\Downloader\Entity\BaseEntity::setFilename()
     */
    public function setFilename($filename)
    {
        $this->cover = $filename;
        parent::setFilename($filename);
    }

    /**
     * Add source
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Source $source
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function addSource(Source $source)
    {
        if (!$this->sources->contains($source)) {
            $this->sources->add($source);
            $source->setItem($this);
        }
        return $this;
    }

    /**
     * Remove source
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Source $source
     */
    public function removeSource(Source $source)
    {
        if ($this->sources->contains($source)) {
            $this->sources->removeElement($source);
            $source->setItem(null);
        }
    }

    /**
     * Get sources
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * Add image
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Image $image
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function addImage(Image $image)
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setItem($this);
        }
        return $this;
    }

    /**
     * Remove image
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Image $image
     */
    public function removeImage(Image $image)
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            $image->setItem(null);
        }
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set number of episodes
     *
     * @param string $episodes_number
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function setEpisodesNumber($episodes_number)
    {
        $this->episodes_number = $episodes_number;
        return $this;
    }

    /**
     * Get number of episodes
     *
     * @return string 
     */
    public function getEpisodesNumber()
    {
        return $this->episodes_number;
    }

    /**
     * Set date add item
     *
     * @param \DateTime $date_add
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function setDateAdd(\DateTime $date_add)
    {
        $this->date_add = clone $date_add;
        return $this;
    }

    /**
     * Get date add item
     *
     * @return \DateTime 
     */
    public function getDateAdd()
    {
        return clone $this->date_add;
    }

    /**
     * Set date last update item
     *
     * @param \DateTime $date_update
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function setDateUpdate(\DateTime $date_update)
    {
        $this->date_update = clone $date_update;
        return $this;
    }

    /**
     * Get date last update item
     *
     * @return \DateTime
     */
    public function getDateUpdate()
    {
        return clone $this->date_update;
    }

    /**
     * Set rating
     *
     * @param integer $rating
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * Get rating
     *
     * @return integer
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Set studio
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Studio $studio
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function setStudio(Studio $studio = null)
    {
        if ($this->studio !== $studio) {
            // romove link on this item for old studio
            if ($this->studio instanceof Studio) {
                $tmp = $this->studio;
                $this->studio = null;
                $tmp->removeItem($this);
            }
            $this->studio = $studio;
            // add link on this item
            if ($this->studio instanceof Studio) {
                $this->studio->addItem($this);
            }
        }
        return $this;
    }

    /**
     * Get studio
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Studio
     */
    public function getStudio()
    {
        return $this->studio;
    }

    /**
     * Change item date update
     *
     * @ORM\PreUpdate
     */
    public function doChangeDateUpdate()
    {
        $this->date_update = new \DateTime();
    }

    /**
     * Set date item add
     *
     * @ORM\PrePersist
     */
    public function doSetDateItemAdd()
    {
        if (!$this->date_add) {
            $this->date_add = new \DateTime();
        }
    }

    /**
     * Is valid path for current type
     *
     * @param \Symfony\Component\Validator\ExecutionContextInterface $context
     */
    public function isPathValid(ExecutionContextInterface $context)
    {
        if ($this->getStorage() instanceof Storage && $this->getStorage()->isPathRequired() && !$this->getPath()) {
            $context->addViolationAt('path', 'Path is required to fill for current type of storage');
        }
    }

    /**
     * Freeze item
     *
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function freez(Registry $doctrine)
    {
        $em = $doctrine->getManager();
        // create reference to existing entity
        if ($this->country) {
            $this->country = $em->getReference(get_class($this->country), $this->country->getId());
        }
        if ($this->storage) {
            $this->storage = $em->getReference(get_class($this->storage), $this->storage->getId());
        }
        $this->type = $em->getReference(get_class($this->type), $this->type->getId());
        foreach ($this->genres as $key => $genre) {
            $this->genres[$key] = $em->getReference(get_class($genre), $genre->getId());
        }
        return $this;
    }

    /**
     * Remove storage path in item path
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function doClearPath()
    {
        if (
            $this->not_cleared_path &&
            $this->getStorage() instanceof Storage &&
            $this->getStorage()->getPath() &&
            strpos($this->not_cleared_path, $this->getStorage()->getPath()) === 0
        ) {
            $this->path = substr($this->not_cleared_path, strlen($this->getStorage()->getPath()));
            $this->not_cleared_path = '';
        }
    }

    /**
     * Get item name for url
     *
     * @return string
     */
    public function getUrlName()
    {
        return trim(preg_replace('/\s+/', '_', $this->name), '_');
    }
}
