<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */
namespace AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item;

use AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use AnimeDb\Bundle\CatalogBundle\Entity\Source;
use AnimeDb\Bundle\CatalogBundle\Entity\Name;
use AnimeDb\Bundle\CatalogBundle\Entity\Item as ItemEntity;

/**
 * Install item.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Gto extends Item
{
    /**
     * @return ItemEntity
     */
    protected function buildItem()
    {
        $item = parent::buildItem()
            ->setCountry($this->getCountry('JP'))
            ->setCover('samples/gto.jpg')
            ->setDatePremiere(new \DateTime('1999-06-30'))
            ->setDateEnd(new \DateTime('2000-09-17'))
            ->setDuration(25)
            ->setEpisodesNumber('43')
            ->setFileInfo($this->translator->trans('+ 2 episode collage', [], 'item'))
            ->setName($this->translator->trans('GTO', [], 'item'))
            ->setStudio($this->getStudio('Pierrot'))
            ->setType($this->getType('tv'))
            ->setSummary($this->translator->trans(
                'Tough on the outside, all heart on the inside, Onizuka turned to the life of a high school teacher '
                .'for less excitement and action... or so he thought. GTO, A.K.A.: Great Teacher Onizuka, is the racy '
                .'story of Onizuka, a former motorcycle gang member who becomes a teacher to make a difference and... '
                .'to meet girls? Using his street smarts to deal with colleagues, students and troublemakers, Onizuka '
                .'finds that he too has many lessons to learn!',
                [],
                'item'
            ))
            ->addGenre($this->getGenre('Comedy'))
            ->addGenre($this->getGenre('Drama'))
            ->addGenre($this->getGenre('School'))
            ->addSource((new Source())->setUrl('http://anidb.net/perl-bin/animedb.pl?show=anime&aid=191'))
            ->addSource((new Source())->setUrl('http://en.wikipedia.org/wiki/Great_Teacher_Onizuka'))
            ->addSource((new Source())->setUrl('http://ja.wikipedia.org/wiki/GTO_(%E6%BC%AB%E7%94%BB)'))
            ->addSource((new Source())->setUrl('http://ru.wikipedia.org/wiki/%D0%9A%D1%80%D1%83%D1%82%D0%BE%D0%B9_%D1%83%D1%87%D0%B8%D1%82%D0%B5%D0%BB%D1%8C_%D0%9E%D0%BD%D0%B8%D0%B4%D0%B7%D1%83%D0%BA%D0%B0'))
            ->addSource((new Source())->setUrl('http://www.allcinema.net/prog/show_c.php?num_c=125613'))
            ->addSource((new Source())->setUrl('http://www.animenewsnetwork.com/encyclopedia/anime.php?id=153'))
            ->addSource((new Source())->setUrl('http://www.fansubs.ru/base.php?id=147'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/245/'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/245/Great_Teacher_Onizuka'))
            ->addSource((new Source())->setUrl('http://www.world-art.ru/animation/animation.php?id=311'))
            ->addSource((new Source())->setUrl('http://shikimori.org/animes/245-great-teacher-onizuka'));

        if ($item->getName() != 'GTO') {
            $item->addName((new Name())->setName('GTO'));
        }
        if ($item->getName() != 'Great Teacher Onizuka') {
            $item->addName((new Name())->setName('Great Teacher Onizuka'));
        }

        return $item;
    }

    /**
     * @param Storage $storage
     *
     * @return Item
     */
    public function setStorage(Storage $storage)
    {
        $this->getItem()->setPath($storage->getPath().'GTO (1999) [TV]'.DIRECTORY_SEPARATOR);

        return parent::setStorage($storage);
    }
}
