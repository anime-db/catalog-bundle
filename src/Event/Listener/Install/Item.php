<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Event\Listener\Install;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Translation\TranslatorInterface;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use AnimeDb\Bundle\CatalogBundle\Entity\Item as ItemEntity;

/**
 * Install item
 *
 * <code>
 * $item = (new Item($em))
 *     ->setStorage($storage)
 *     ->getItem();
 * </code>
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Listener\Install
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
     * Translator
     *
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

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
     * @param \Symfony\Component\Translation\TranslatorInterface $translator
     */
    public function __construct(ObjectManager $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
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
     * Get item
     *
     * @return \AnimeDb\Bundle\CatalogBundle\Entity\Item
     */
    public function getItem()
    {
        if (!$this->item) {
            $this->item = $this->buildItem();
        }
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
