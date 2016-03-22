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
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;

/**
 * Country
 *
 * @ORM\Entity
 * @ORM\Table(name="country")
 * @Gedmo\TranslationEntity(class="AnimeDb\Bundle\CatalogBundle\Entity\CountryTranslation")
 * @Annotation\IgnoreAnnotation("ORM")
 *
 * @package AnimeDb\Bundle\CatalogBundle\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Country implements Translatable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=2)
     * @Assert\NotBlank()
     * @Assert\Country()
     *
     * @var int
     */
    protected $id = 0;

    /**
     * @ORM\Column(type="string", length=16)
     * @Assert\NotBlank()
     * @Gedmo\Translatable
     *
     * @var string
     */
    protected $name = '';

    /**
     * @ORM\OneToMany(targetEntity="Item", mappedBy="country")
     *
     * @var ArrayCollection
     */
    protected $items;

    /**
     * Entity locale
     *
     * @Gedmo\Locale
     *
     * @var string
     */
    protected $locale = '';

    /**
     * @ORM\OneToMany(
     *     targetEntity="CountryTranslation",
     *     mappedBy="object",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $translations;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    /**
     * @param string $id
     *
     * @return Country
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return Country
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
     * @return Country
     */
    public function addItem(Item $item)
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setCountry($this);
        }
        return $this;
    }

    /**
     * @param Item $item
     *
     * @return Country
     */
    public function removeItem(Item $item)
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
            $item->setCountry(null);
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
     * @param string $locale
     *
     * @return Country
     */
    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * @return string
     */
    public function getTranslatableLocale()
    {
        return $this->locale;
    }

    /**
     * @return ArrayCollection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * @param CountryTranslation $trans
     *
     * @return Country
     */
    public function addTranslation(CountryTranslation $trans)
    {
        if (!$this->translations->contains($trans)) {
            $this->translations->add($trans);
            $trans->setObject($this);
        }
        return $this;
    }

    /**
     * @param CountryTranslation $trans
     *
     * @return Country
     */
    public function removeTranslation(CountryTranslation $trans)
    {
        if ($this->translations->contains($trans)) {
            $this->translations->removeElement($trans);
            $trans->setObject(null);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
