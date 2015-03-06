<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item;

use AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use AnimeDb\Bundle\CatalogBundle\Entity\Source;
use AnimeDb\Bundle\CatalogBundle\Entity\Name;

/**
 * Install item
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class Hellsing extends Item
{
    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\CatalogBundle\Service\Install\Item::buildItem()
     */
    protected function buildItem()
    {
        $item = parent::buildItem()
            ->setCountry($this->getCountry('JP'))
            ->setCover('samples/hellsing.jpg')
            ->setDatePremiere(new \DateTime('2006-02-10'))
            ->setDateEnd(new \DateTime('2012-12-26'))
            ->setDuration(50)
            ->setEpisodes($this->translator->trans(
'1. Hellsing I (10.02.2006, 50 min.)
2. Hellsing II (25.08.2006, 45 min.)
3. Hellsing III (04.04.2007, 50 min.)
4. Hellsing IV (22.02.2008, 55 min.)
5. Hellsing V (21.11.2008, 40 min.)
6. Hellsing VI (24.07.2009, 40 min.)
7. Hellsing VII (23.12.2009, 45 min.)
8. Hellsing VIII (27.07.2011, 50 min.)
9. Hellsing IX (15.02.2012, 45 min.)
10. Hellsing X (26.12.2012, 65 min.)',
                [],
                'item'
            ))
            ->setEpisodesNumber('10')
            ->setFileInfo($this->translator->trans('+ 4 specials', [], 'item'))
            ->setName($this->translator->trans('Hellsing', [], 'item'))
            ->setStudio($this->getStudio('Satelight'))
            ->setType($this->getType('ova'))
            ->setSummary($this->translator->trans(
                'Hellsing, a secret organization of the British government, have long been battling supernatural '
                .'threats to keep the people safe from the creatures of the night. The current leader, Integra '
                .'Wingates Hellsing controls her own personal army to eliminate the undead beings, but even her '
                .'highly trained soldiers pale in comparison to her most trusted vampire exterminator, a man by the '
                .'name of Alucard, who is actually a powerful vampire himself. Along with Integra\'s mysterious '
                .'butler and Alucard\'s new vampire minion, Seras Victoria, The Hellsing Organization must face not '
                .'only regular ghouls and vampires, but a rivalling secret organization from the Vatican, and '
                .'Millennium, an enigmatic group of madmen spawned by a certain war over 50 years ago...',
                [],
                'item'
            ))
            ->addGenre($this->getGenre('Drama'))
            ->addGenre($this->getGenre('Adventure'))
            ->addGenre($this->getGenre('Mystery'))
            ->addSource((new Source())->setUrl('http://anidb.net/perl-bin/animedb.pl?show=anime&aid=3296'))
            ->addSource((new Source())->setUrl('http://en.wikipedia.org/wiki/Hellsing_%28manga%29'))
            ->addSource((new Source())->setUrl('http://ja.wikipedia.org/wiki/HELLSING'))
            ->addSource((new Source())->setUrl('http://ru.wikipedia.org/wiki/%D0%A5%D0%B5%D0%BB%D0%BB%D1%81%D0%B8%D0%BD%D0%B3:_%D0%92%D0%BE%D0%B9%D0%BD%D0%B0_%D1%81_%D0%BD%D0%B5%D1%87%D0%B8%D1%81%D1%82%D1%8C%D1%8E'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/777/'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/777/Hellsing_Ultimate'))
            ->addSource((new Source())->setUrl('http://uanime.org.ua/anime/63.html'))
            ->addSource((new Source())->setUrl('http://www.allcinema.net/prog/show_c.php?num_c=323337'))
            ->addSource((new Source())->setUrl('http://www.animenewsnetwork.com/encyclopedia/anime.php?id=5114'))
            ->addSource((new Source())->setUrl('http://www.fansubs.ru/base.php?id=988'))
            ->addSource((new Source())->setUrl('http://www.world-art.ru/animation/animation.php?id=4340'))
            ->addSource((new Source())->setUrl('http://shikimori.org/animes/777-hellsing-ultimate'));

        if ($item->getName() != 'Hellsing') {
            $item->addName((new Name())->setName('Hellsing'));
        }
        if ($item->getName() != 'Hellsing Ultimate') {
            $item->addName((new Name())->setName('Hellsing Ultimate'));
        }

        return $item;
    }

    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\CatalogBundle\Service\Install\Item::setStorage()
     */
    public function setStorage(Storage $storage)
    {
        $this->getItem()->setPath($storage->getPath().'Hellsing (2006) [OVA]'.DIRECTORY_SEPARATOR);
        return parent::setStorage($storage);
    }
}
