<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Item search.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Search
{
    /**
     * @Assert\Date()
     *
     * @var \DateTime|null
     */
    protected $date_add;

    /**
     * @Assert\Date()
     *
     * @var \DateTime|null
     */
    protected $date_end;

    /**
     * @Assert\Date()
     *
     * @var \DateTime|null
     */
    protected $date_premiere;

    /**
     * @var ArrayCollection
     */
    protected $genres;

    /**
     * @var ArrayCollection
     */
    protected $labels;

    /**
     * @var Country|null
     */
    protected $country;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var Storage|null
     */
    protected $storage;

    /**
     * @var Type|null
     */
    protected $type;

    /**
     * @var Studio|null
     */
    protected $studio;

    public function __construct()
    {
        $this->genres = new ArrayCollection();
        $this->labels = new ArrayCollection();
    }

    /**
     * @param string $name
     *
     * @return Search
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
     * @param \DateTime|null $date_add
     *
     * @return Search
     */
    public function setDateAdd(\DateTime $date_add = null)
    {
        $this->date_add = $date_add ? clone $date_add : $date_add;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateAdd()
    {
        return $this->date_add ? clone $this->date_add : null;
    }

    /**
     * @param \DateTime|null $date_premiere
     *
     * @return Search
     */
    public function setDatePremiere(\DateTime $date_premiere = null)
    {
        $this->date_premiere = $date_premiere ? clone $date_premiere : $date_premiere;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDatePremiere()
    {
        return $this->date_premiere ? clone $this->date_premiere : null;
    }

    /**
     * @param \DateTime|null $date_end
     *
     * @return Search
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
     * @param Genre $genre
     *
     * @return Search
     */
    public function addGenre(Genre $genre)
    {
        if (!$this->genres->contains($genre)) {
            $this->genres->add($genre);
        }

        return $this;
    }

    /**
     * @param Genre $genre
     *
     * @return Search
     */
    public function removeGenre(Genre $genre)
    {
        $this->genres->removeElement($genre);

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
     * @return Search
     */
    public function addLabel(Label $label)
    {
        if (!$this->labels->contains($label)) {
            $this->labels->add($label);
        }

        return $this;
    }

    /**
     * @param Label $label
     *
     * @return Search
     */
    public function removeLabel(Label $label)
    {
        $this->labels->removeElement($label);

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
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param Country|null $country
     *
     * @return Search
     */
    public function setCountry(Country $country = null)
    {
        if ($this->country !== $country) {
            $this->country = $country;
        }

        return $this;
    }

    /**
     * @param Storage|null $storage
     *
     * @return Search
     */
    public function setStorage(Storage $storage = null)
    {
        if ($this->storage !== $storage) {
            $this->storage = $storage;
        }

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
     * @param Type $type
     *
     * @return Search
     */
    public function setType(Type $type = null)
    {
        if ($this->type !== $type) {
            $this->type = $type;
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
     * @param Studio $studio
     *
     * @return Search
     */
    public function setStudio(Studio $studio = null)
    {
        if ($this->studio !== $studio) {
            $this->studio = $studio;
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
}
