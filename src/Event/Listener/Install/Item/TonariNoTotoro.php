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
use AnimeDb\Bundle\CatalogBundle\Entity\Item as ItemEntity;

/**
 * Install item
 *
 * @package AnimeDb\Bundle\CatalogBundle\Event\Listener\Install\Item
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class TonariNoTotoro extends Item
{
    /**
     * @return ItemEntity
     */
    protected function buildItem()
    {
        $item = parent::buildItem()
            ->setCountry($this->getCountry('JP'))
            ->setCover('samples/tonari-no-totoro.jpg')
            ->setDatePremiere(new \DateTime('1988-04-16'))
            ->setDuration(88)
            ->setEpisodesNumber('1')
            ->setName($this->translator->trans('Tonari no Totoro', [], 'item'))
            ->setStudio($this->getStudio('Studio Ghibli'))
            ->setType($this->getType('feature'))
            ->setSummary($this->translator->trans(
                'Totoro is a forest spirit that little Mei, and later her older sister Satsuki, encounter in a giant '
                .'camphor tree near their new home in the countryside. Although their father Kusakabe Tatsuo, a '
                .'university professor, is with them when they move, their mother Yasuko is in the hospital, '
                .'recovering from some unnamed illness. When Mei hears that her mother\'s condition may be getting '
                .'worse, she resolves to visit her all by herself. When everyone realizes she is missing, only Totoro '
                .'knows how to find her!',
                [],
                'item'
            ))
            ->addGenre($this->getGenre('Comedy'))
            ->addGenre($this->getGenre('Drama'))
            ->addGenre($this->getGenre('Fable'))
            ->addGenre($this->getGenre('Adventure'))
            ->addSource((new Source())->setUrl('http://anidb.net/perl-bin/animedb.pl?show=anime&aid=303'))
            ->addSource((new Source())->setUrl('http://en.wikipedia.org/wiki/My_Neighbor_Totoro'))
            ->addSource((new Source())->setUrl('http://ja.wikipedia.org/wiki/%E3%81%A8%E3%81%AA%E3%82%8A%E3%81%AE%E3%83%88%E3%83%88%E3%83%AD'))
            ->addSource((new Source())->setUrl('http://ru.wikipedia.org/wiki/%D0%9D%D0%B0%D1%88_%D1%81%D0%BE%D1%81%D0%B5%D0%B4_%D0%A2%D0%BE%D1%82%D0%BE%D1%80%D0%BE'))
            ->addSource((new Source())->setUrl('http://uanime.org.ua/anime/145.html'))
            ->addSource((new Source())->setUrl('http://www.allcinema.net/prog/show_c.php?num_c=150435'))
            ->addSource((new Source())->setUrl('http://www.animenewsnetwork.com/encyclopedia/anime.php?id=534'))
            ->addSource((new Source())->setUrl('http://www.fansubs.ru/base.php?id=266'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/523/'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/523/Tonari_no_Totoro'))
            ->addSource((new Source())->setUrl('http://www.world-art.ru/animation/animation.php?id=62'))
            ->addSource((new Source())->setUrl('http://shikimori.org/animes/523-tonari-no-totoro'));

        if ($item->getName() != 'Tonari no Totoro') {
            $item->addName((new Name())->setName('Tonari no Totoro'));
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
        $this->getItem()->setPath($storage->getPath().'Tonari no Totoro (1988)'.DIRECTORY_SEPARATOR);
        return parent::setStorage($storage);
    }
}
