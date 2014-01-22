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

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use AnimeDb\Bundle\CatalogBundle\Entity\Genre;
use AnimeDb\Bundle\CatalogBundle\Entity\Country;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use AnimeDb\Bundle\CatalogBundle\Entity\Type;
use AnimeDb\Bundle\CatalogBundle\Entity\Studio;

/**
 * Item search
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Search
{
    /**
     * Date add item
     *
     * @Assert\Date()
     *
     * @var \DateTime|null
     */
    protected $date_add;

    /**
     * Date end release
     *
     * @Assert\Date()
     *
     * @var \DateTime|null
     */
    protected $date_end;

    /**
     * Date premiere
     *
     * @Assert\Date()
     *
     * @var \DateTime|null
     */
    protected $date_premiere;

    /**
     * Genres
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $genres;

    /**
     * Country
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Country|null
     */
    protected $country;

    /**
     * Main name
     *
     * @var string
     */
    protected $name;

    /**
     * Storage
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Storage|null
     */
    protected $storage;

    /**
     * Type
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Type|null
     */
    protected $type;

    /**
     * Studio
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Studio|null
     */
    protected $studio;

    /**
     * Construct
     */
    public function __construct() {
        $this->genres  = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Search
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
     * Set date add
     *
     * @param \DateTime|null $date_add
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Search
     */
    public function setDateAdd(\DateTime $date_add = null)
    {
        $this->date_add = $date_add ? clone $date_add : $date_add;
        return $this;
    }

    /**
     * Get date add
     *
     * @return \DateTime|null
     */
    public function getDateAdd()
    {
        return $this->date_add ? clone $this->date_add : null;
    }

    /**
     * Set date premiere
     *
     * @param \DateTime|null $date_premiere
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Search
     */
    public function setDatePremiere(\DateTime $date_premiere = null)
    {
        $this->date_premiere = $date_premiere ? clone $date_premiere : $date_premiere;
        return $this;
    }

    /**
     * Get date premiere
     *
     * @return \DateTime|null
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
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Search
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
     * Set genre
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Genre $genre
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Search
     */
    public function setGenre(Genre $genre)
    {
        $this->genre = $genre;
        return $this;
    }

    /**
     * Add genres
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Genre $genre
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Search
     */
    public function addGenre(Genre $genre)
    {
        $this->genres->add($genre);
        return $this;
    }

    /**
     * Remove genres
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Genre $genre
     */
    public function removeGenre(Genre $genre)
    {
        $this->genres->removeElement($genre);
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
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Search
     */
    public function setStorage(Storage $storage = null)
    {
        if ($this->storage !== $storage) {
            $this->storage = $storage;
        }
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
     * Set type
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Type $type
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Search
     */
    public function setType(Type $type = null)
    {
        if ($this->type !== $type) {
            $this->type = $type;
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
     * Set studio
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Studio $studio
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Search
     */
    public function setStudio(Studio $studio = null)
    {
        if ($this->studio !== $studio) {
            $this->studio = $studio;
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
}