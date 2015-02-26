<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Service\Install;

use Doctrine\Common\Persistence\ObjectManager;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use AnimeDb\Bundle\CatalogBundle\Entity\Item as ItemEntity;

/**
 * Install item
 *
 * <code>
 * $item = (new Item($em))
 *     ->setStorage($storage)
 *     ->setLocale($locale)
 *     ->getItem();
 * </code>
 *
 * @package AnimeDb\Bundle\CatalogBundle\Service\Install
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
abstract class Item
{
    /**
     * Entity manager
     *
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $em;

    /**
     * Item
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    private $item;

    /**
     * Construct
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $em
     */
    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
        $this->item = $this->buildItem();
    }

    /**
     * Build item
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    protected function buildItem()
    {
        return new ItemEntity();
    }

    /**
     * Set storage
     *
     * Heir sets the path to the item files
     *
     * @param \AnimeDb\Bundle\CatalogBundle\Entity\Storage $storage
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Service\Install\Item
     */
    public function setStorage(Storage $storage)
    {
        $this->getItem()->setStorage($storage);
        return $this;
    }

    /**
     * Set locale
     *
     * Heir configures item in accordance with locale
     *
     * @param string $locale
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Service\Install\Item
     */
    public function setLocale($locale)
    {
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
     * Get country
     *
     * @param string $name
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Country
     */
    protected function getCountry($name)
    {
        return $this->em->getRepository('AnimeDbCatalogBundle:Country')->find($name);
    }

    /**
     * Get type
     *
     * @param string $id
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Type
     */
    protected function getType($id)
    {
        return $this->em->getRepository('AnimeDbCatalogBundle:Type')->find($id);
    }

    /**
     * Get studio
     *
     * @param string $name
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Studio
     */
    protected function getStudio($name)
    {
        return $this->em->getRepository('AnimeDbCatalogBundle:Studio')->findOneBy(['name' => $name]);
    }

    /**
     * Get genre
     *
     * @param string $name
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Genre
     */
    protected function getGenre($name)
    {
        return $this->em->getRepository('AnimeDbCatalogBundle:Genre')->findOneBy(['name' => $name]);
    }
}