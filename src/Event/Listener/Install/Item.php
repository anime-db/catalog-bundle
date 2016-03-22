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

use AnimeDb\Bundle\CatalogBundle\Entity\Country;
use AnimeDb\Bundle\CatalogBundle\Entity\Genre;
use AnimeDb\Bundle\CatalogBundle\Entity\Studio;
use AnimeDb\Bundle\CatalogBundle\Entity\Type;
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
     * @var ObjectManager
     */
    private $em;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var Item
     */
    private $item;

    /**
     * @param ObjectManager $em
     * @param TranslatorInterface $translator
     */
    public function __construct(ObjectManager $em, TranslatorInterface $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }

    /**
     * @return ItemEntity
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
     * @param Storage $storage
     *
     * @return Item
     */
    public function setStorage(Storage $storage)
    {
        $this->getItem()->setStorage($storage);
        return $this;
    }

    /**
     * @return ItemEntity
     */
    public function getItem()
    {
        if (!$this->item) {
            $this->item = $this->buildItem();
        }
        return $this->item;
    }

    /**
     * @param string $name
     *
     * @return Country
     */
    protected function getCountry($name)
    {
        return $this->em->getRepository('AnimeDbCatalogBundle:Country')->find($name);
    }

    /**
     * @param string $id
     *
     * @return Type
     */
    protected function getType($id)
    {
        return $this->em->getRepository('AnimeDbCatalogBundle:Type')->find($id);
    }

    /**
     * @param string $name
     *
     * @return Studio
     */
    protected function getStudio($name)
    {
        return $this->em->getRepository('AnimeDbCatalogBundle:Studio')->findOneBy(['name' => $name]);
    }

    /**
     * @param string $name
     *
     * @return Genre
     */
    protected function getGenre($name)
    {
        return $this->em->getRepository('AnimeDbCatalogBundle:Genre')->findOneBy(['name' => $name]);
    }
}
