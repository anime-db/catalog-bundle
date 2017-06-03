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
class Gintama extends Item
{
    /**
     * @return ItemEntity
     */
    protected function buildItem()
    {
        $item = parent::buildItem()
            ->setCountry($this->getCountry('JP'))
            ->setCover('samples/gintama.jpg')
            ->setDatePremiere(new \DateTime('2006-04-04'))
            ->setDateEnd(new \DateTime('2010-03-25'))
            ->setDuration(25)
            ->setEpisodesNumber('201')
            ->setName($this->translator->trans('Gintama', [], 'item'))
            ->setStudio($this->getStudio('Sunrise'))
            ->setType($this->getType('tv'))
            ->setSummary($this->translator->trans(
                'Sakata Gintoki is a samurai living in an era when samurais are no longer needed. To add to his '
                .'troubles, oppressive aliens have moved in to invade. Gintoki lives with Kagura and Shimura '
                .'Shinpachi, taking on odd jobs to make the world a better place... and to pay their rent.',
                [],
                'item'
            ))
            ->addGenre($this->getGenre('Comedy'))
            ->addGenre($this->getGenre('Adventure'))
            ->addGenre($this->getGenre('Sci-fi'))
            ->addSource((new Source())->setUrl('http://anidb.net/perl-bin/animedb.pl?show=anime&aid=3468'))
            ->addSource((new Source())->setUrl('http://cal.syoboi.jp/tid/853/time'))
            ->addSource((new Source())->setUrl('http://en.wikipedia.org/wiki/Gintama'))
            ->addSource((new Source())->setUrl('http://ja.wikipedia.org/wiki/%E9%8A%80%E9%AD%82_%28%E3%82%A2%E3%83%8B%E3%83%A1%29'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/918/'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/918/Gintama'))
            ->addSource((new Source())->setUrl('http://ru.wikipedia.org/wiki/Gintama'))
            ->addSource((new Source())->setUrl('http://www.allcinema.net/prog/show_c.php?num_c=324863'))
            ->addSource((new Source())->setUrl('http://www.animenewsnetwork.com/encyclopedia/anime.php?id=6236'))
            ->addSource((new Source())->setUrl('http://www.fansubs.ru/base.php?id=2022'))
            ->addSource((new Source())->setUrl('http://www.world-art.ru/animation/animation.php?id=5013'))
            ->addSource((new Source())->setUrl('http://shikimori.org/animes/918-gintama'));

        if ($item->getName() != 'Gintama') {
            $item->addName((new Name())->setName('Gintama'));
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
        $this->getItem()->setPath($storage->getPath().'Gintama (2006) [TV-1]'.DIRECTORY_SEPARATOR);

        return parent::setStorage($storage);
    }
}
