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
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Storage of item files
 *
 * @ORM\Entity
 * @ORM\Table(name="storage")
 * @Assert\Callback(methods={"isPathValid"})
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="AnimeDb\Bundle\CatalogBundle\Repository\Storage")
 * @Annotation\IgnoreAnnotation("ORM")
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Storage
{
    /**
     * Type folder on computer (local/network)
     *
     * @var string
     */
    const TYPE_FOLDER = 'folder';

    /**
     * Type external storage (HDD/Flash/SD)
     *
     * @var string
     */
    const TYPE_EXTERNAL = 'external';

    /**
     * Type external storage read-only (CD/DVD)
     *
     * @var string
     */
    const TYPE_EXTERNAL_R = 'external-r';

    /**
     * Type video storage (DVD/BD/VHS)
     *
     * @var string
     */
    const TYPE_VIDEO = 'video';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $id = 0;

    /**
     * @ORM\Column(type="string", length=128)
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $name = '';

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $description = '';

    /**
     * @ORM\Column(type="string", length=16)
     * @Assert\Choice(callback = "getTypes")
     *
     * @var string
     */
    protected $type = '';

    /**
     * Path on computer
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    protected $path = '';

    /**
     * Date last update storage
     *
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $date_update;

    /**
     * Date of files last modified
     *
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    protected $file_modified;

    /**
     * @ORM\OneToMany(targetEntity="Item", mappedBy="storage")
     *
     * @var ArrayCollection
     */
    protected $items;

    /**
     * List old paths
     *
     * @var array
     */
    protected $old_paths = [];

    /**
     * @var array
     */
    protected static $type_names = [
        self::TYPE_FOLDER,
        self::TYPE_EXTERNAL,
        self::TYPE_EXTERNAL_R,
        self::TYPE_VIDEO
    ];

    /**
     * @var array
     */
    protected static $type_titles = [
        self::TYPE_FOLDER => 'Folder on computer (local/network)',
        self::TYPE_EXTERNAL => 'External storage (HDD/Flash/SD)',
        self::TYPE_EXTERNAL_R => 'External storage read-only (CD/DVD)',
        self::TYPE_VIDEO => 'Video storage (DVD/BD/VHS)'
    ];

    public function __construct()
    {
        $this->items = new ArrayCollection();
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
     * @return Storage
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
     * @param string $description
     *
     * @return Storage
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $path
     *
     * @return Storage
     */
    public function setPath($path)
    {
        if ($this->path) {
            $this->old_paths[] = $this->path;
        }
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return array
     */
    public function getOldPaths()
    {
        return $this->old_paths;
    }

    /**
     * @param Item $item
     *
     * @return Storage
     */
    public function addItem(Item $item)
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setStorage($this);
        }
        return $this;
    }

    /**
     * @param Item $item
     *
     * @return Storage
     */
    public function removeItem(Item $item)
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            $item->setStorage(null);
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
     * @param string $type
     *
     * @return Storage
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return self::$type_names;
    }

    /**
     * @return array
     */
    public static function getTypeTitles()
    {
        return self::$type_titles;
    }

    /**
     * Get title for current type
     *
     * @return string
     */
    public function getTypeTitle()
    {
        return isset(self::$type_titles[$this->type]) ? self::$type_titles[$this->type] : '';
    }

    /**
     * Get types storage allow write
     *
     * @return array
     */
    public static function getTypesWritable()
    {
        return [self::TYPE_FOLDER, self::TYPE_EXTERNAL];
    }

    /**
     * Get types storage allow read
     *
     * @return array
     */
    public static function getTypesReadable()
    {
        return [self::TYPE_FOLDER, self::TYPE_EXTERNAL, self::TYPE_EXTERNAL_R];
    }

    /**
     * Is path required to fill for current type of storage
     *
     * @return bool
     */
    public function isPathRequired()
    {
        return $this->isWritable();
    }

    /**
     * @return bool
     */
    public function isWritable()
    {
        return in_array($this->getType(), self::getTypesWritable());
    }

    /**
     * @return bool
     */
    public function isReadable()
    {
        return in_array($this->getType(), self::getTypesReadable());
    }

    /**
     * Is valid path for current type
     *
     * @param ExecutionContextInterface $context
     */
    public function isPathValid(ExecutionContextInterface $context)
    {
        if ($this->isPathRequired() && !$this->getPath()) {
            $context->addViolationAt('path', 'Path is required to fill for current type of storage');
        }
    }

    /**
     * @param \DateTime $date_update
     *
     * @return Storage
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
        return $this->date_update;
    }

    /**
     * @param \DateTime $file_modified
     *
     * @return Storage
     */
    public function setFileModified(\DateTime $file_modified)
    {
        $this->file_modified = clone $file_modified;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getFileModified()
    {
        return $this->file_modified;
    }

    /**
     * @ORM\PreUpdate
     */
    public function doChangeDateUpdate()
    {
        $this->date_update = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
