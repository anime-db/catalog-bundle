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
use Symfony\Component\Validator\ExecutionContextInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use AnimeDb\Bundle\AppBundle\Service\Downloader\Entity\BaseEntity;
use AnimeDb\Bundle\AppBundle\Service\Downloader\Entity\ImageInterface;

/**
 * Item.
 *
 * @ORM\Entity
 * @ORM\Table(name="item")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="AnimeDb\Bundle\CatalogBundle\Repository\Item")
 * @Assert\Callback(methods={"isPathValid"})
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Item extends BaseEntity implements ImageInterface
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
     * @ORM\OneToMany(targetEntity="Name", mappedBy="item", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @var ArrayCollection
     */
    protected $names;

    /**
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="items", cascade={"persist"})
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     *
     * @var Type
     */
    protected $type;

    /**
     * @ORM\Column(type="date")
     * @Assert\Date()
     *
     * @var \DateTime
     */
    protected $date_premiere;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date()
     *
     * @var \DateTime|null
     */
    protected $date_end;

    /**
     * @ORM\ManyToMany(targetEntity="Genre", inversedBy="items", cascade={"persist"})
     * @ORM\JoinTable(name="items_genres")
     *
     * @var ArrayCollection
     */
    protected $genres;

    /**
     * @ORM\ManyToMany(targetEntity="Label", inversedBy="items", cascade={"persist"})
     * @ORM\JoinTable(name="items_labels")
     *
     * @var ArrayCollection
     */
    protected $labels;

    /**
     * @ORM\ManyToOne(targetEntity="Country", inversedBy="items", cascade={"persist"})
     * @ORM\JoinColumn(name="country", referencedColumnName="id")
     *
     * @var Country
     */
    protected $country;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(type="integer", message="The value {{ value }} is not a valid {{ type }}.")
     *
     * @var int
     */
    protected $duration = 0;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $summary = '';

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     *
     * @var string
     */
    protected $path = '';

    /**
     * @ORM\ManyToOne(targetEntity="Storage", inversedBy="items", cascade={"persist"})
     * @ORM\JoinColumn(name="storage", referencedColumnName="id")
     *
     * @var Storage
     */
    protected $storage;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $episodes = '';

    /**
     * Translate (subtitles and voice).
     *
     * @ORM\Column(type="string", length=256, nullable=true)
     *
     * @var string
     */
    protected $translate = '';

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $file_info = '';

    /**
     * @ORM\OneToMany(targetEntity="Source", mappedBy="item", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @var ArrayCollection
     */
    protected $sources;

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     *
     * @var string
     */
    protected $cover = '';

    /**
     * Number of episodes.
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
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $date_add;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $date_update;

    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="item", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @var ArrayCollection
     */
    protected $images;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(type="integer", message="The value {{ value }} is not a valid {{ type }}.")
     *
     * @var int
     */
    protected $rating = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Studio", inversedBy="items", cascade={"persist"})
     * @ORM\JoinColumn(name="studio", referencedColumnName="id")
     *
     * @var Studio
     */
    protected $studio;

    /**
     * @var string
     */
    protected $not_cleared_path = '';

    public function __construct()
    {
        $this->genres = new ArrayCollection();
        $this->labels = new ArrayCollection();
        $this->names = new ArrayCollection();
        $this->sources = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->date_add = new \DateTime();
        $this->date_update = new \DateTime();
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
     * @return Item
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
     * @param \DateTime|null $date_premiere
     *
     * @return Item
     */
    public function setDatePremiere(\DateTime $date_premiere = null)
    {
        $this->date_premiere = $date_premiere ? clone $date_premiere : $date_premiere;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDatePremiere()
    {
        return $this->date_premiere ? clone $this->date_premiere : null;
    }

    /**
     * @param \DateTime|null $date_end
     *
     * @return Item
     */
    public function setDateEnd(\DateTime $date_end = null)
    {
        $this->date_end = $date_end ? clone $date_end : null;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateEnd()
    {
        return $this->date_end ? clone $this->date_end : null;
    }

    /**
     * @param int $duration
     *
     * @return Item
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param string $summary
     *
     * @return Item
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param string $path
     *
     * @return Item
     */
    public function setPath($path)
    {
        if ($path) {
            $this->not_cleared_path = $path;
            $this->doClearPath();
        } else {
            $this->path = '';
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
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
     * Get real path.
     *
     * Need for tests
     *
     * @return string
     */
    public function getRealPath()
    {
        return $this->path;
    }

    /**
     * @param string $episodes
     *
     * @return Item
     */
    public function setEpisodes($episodes)
    {
        $this->episodes = $episodes;

        return $this;
    }

    /**
     * @return string
     */
    public function getEpisodes()
    {
        return $this->episodes;
    }

    /**
     * @param string $translate
     *
     * @return Item
     */
    public function setTranslate($translate)
    {
        $this->translate = $translate;

        return $this;
    }

    /**
     * @return string
     */
    public function getTranslate()
    {
        return $this->translate;
    }

    /**
     * @param string $fileInfo
     *
     * @return Item
     */
    public function setFileInfo($fileInfo)
    {
        $this->file_info = $fileInfo;

        return $this;
    }

    /**
     * @return string
     */
    public function getFileInfo()
    {
        return $this->file_info;
    }

    /**
     * @param Name $name
     *
     * @return Item
     */
    public function addName(Name $name)
    {
        $names = array_map('strval', $this->names->toArray());
        if (!in_array($name->getName(), $names)) {
            $this->names->add($name);
            $name->setItem($this);
        }

        return $this;
    }

    /**
     * @param Name $name
     *
     * @return Item
     */
    public function removeName(Name $name)
    {
        if ($this->names->contains($name)) {
            $this->names->removeElement($name);
            $name->setItem(null);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * @param Type $type
     *
     * @return Item
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
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param Genre $genre
     *
     * @return Item
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
     * @param Genre $genre
     *
     * @return Item
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
     * @return ArrayCollection
     */
    public function getGenres()
    {
        return $this->genres;
    }

    /**
     * @param Label $label
     *
     * @return Item
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
     * @param Label $label
     *
     * @return Item
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
     * @return ArrayCollection
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param Country $country
     *
     * @return Item
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
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param Storage $storage
     *
     * @return Item
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
     * @return Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param string $cover
     *
     * @return Item
     */
    public function setCover($cover)
    {
        $this->setFilename($cover);

        return $this;
    }

    /**
     * @return string
     */
    public function getCover()
    {
        return $this->getFilename();
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->cover ?: parent::getFilename();
    }

    /**
     * @param string $filename
     *
     * @return Item
     */
    public function setFilename($filename)
    {
        $this->cover = $filename;
        parent::setFilename($filename);

        return $this;
    }

    /**
     * @param Source $source
     *
     * @return Item
     */
    public function addSource(Source $source)
    {
        $sources = array_map('strval', $this->sources->toArray());
        if (!in_array($source->getUrl(), $sources)) {
            $this->sources->add($source);
            $source->setItem($this);
        }

        return $this;
    }

    /**
     * @param Source $source
     *
     * @return Item
     */
    public function removeSource(Source $source)
    {
        if ($this->sources->contains($source)) {
            $this->sources->removeElement($source);
            $source->setItem(null);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * @param Image $image
     *
     * @return Item
     */
    public function addImage(Image $image)
    {
        $images = array_map('strval', $this->images->toArray());
        if (!in_array($image->getSource(), $images)) {
            $this->images->add($image);
            $image->setItem($this);
        }

        return $this;
    }

    /**
     * @param Image $image
     *
     * @return Item
     */
    public function removeImage(Image $image)
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            $image->setItem(null);
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param string $episodes_number
     *
     * @return Item
     */
    public function setEpisodesNumber($episodes_number)
    {
        $this->episodes_number = $episodes_number;

        return $this;
    }

    /**
     * @return string
     */
    public function getEpisodesNumber()
    {
        return $this->episodes_number;
    }

    /**
     * @param \DateTime $date_add
     *
     * @return Item
     */
    public function setDateAdd(\DateTime $date_add)
    {
        $this->date_add = clone $date_add;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateAdd()
    {
        return clone $this->date_add;
    }

    /**
     * @param \DateTime $date_update
     *
     * @return Item
     */
    public function setDateUpdate(\DateTime $date_update)
    {
        $this->date_update = clone $date_update;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdate()
    {
        return clone $this->date_update;
    }

    /**
     * @param int $rating
     *
     * @return Item
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param Studio $studio
     *
     * @return Item
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
     * @return Studio
     */
    public function getStudio()
    {
        return $this->studio;
    }

    /**
     * @ORM\PreUpdate
     */
    public function doChangeDateUpdate()
    {
        $this->date_update = new \DateTime();
    }

    /**
     * Is valid path for current type.
     *
     * @param ExecutionContextInterface $context
     */
    public function isPathValid(ExecutionContextInterface $context)
    {
        if ($this->getStorage() instanceof Storage && $this->getStorage()->isPathRequired() && !$this->getPath()) {
            $context->addViolationAt('path', 'Path is required to fill for current type of storage');
        }
    }

    /**
     * Freeze item.
     *
     * @param Registry $doctrine
     *
     * @return Item
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
     * Remove storage path in item path.
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
     * Get item name for url.
     *
     * @return string
     */
    public function getUrlName()
    {
        return trim(preg_replace('/\s+/', '_', $this->name), '_');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
