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
class SpiritedAway extends Item
{
    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\CatalogBundle\Service\Install\Item::buildItem()
     */
    protected function buildItem()
    {
        $item = parent::buildItem()
            ->setCountry($this->getCountry('JP'))
            ->setCover('samples/spirited-away.jpg')
            ->setDatePremiere(new \DateTime('2001-07-20'))
            ->setDuration(125)
            ->setEpisodesNumber('1')
            ->setName($this->translator->trans('Spirited Away', [], 'item'))
            ->setStudio($this->getStudio('Studio Ghibli'))
            ->setType($this->getType('feature'))
            ->setSummary($this->translator->trans(
                'On the way to their new home, 10-year-old Chihiro Ogino\'s family stumbles upon a deserted theme '
                .'park. Intrigued, the family investigates the park, though unbeknownst to them, it is secretly '
                .'inhabited by spirits who sleep by day and appear at night. When Chihiro\'s mother and father '
                .'eat food from a restaurant in the street, angry spirits turn them into pigs. Furthermore, a '
                .'wide sea has appeared between the spirit world and the human one, trapping Chihiro, the sole '
                .'human, in a land of spirits. Luckily for her though, a mysterious boy named Haku appears, '
                .'claiming to know her from the past. Under his instructions, Chihiro secures a job in the '
                .'bathhouse where Haku works. With only her courage and some new found friends to aid her, '
                .'Chihiro embarks on a journey to turn her parents back to their original forms and return home.',
                [],
                'item'
            ))
            ->addGenre($this->getGenre('Adventure'))
            ->addGenre($this->getGenre('Drama'))
            ->addGenre($this->getGenre('Fable'))
            ->addSource((new Source())->setUrl('http://www.animenewsnetwork.com/encyclopedia/anime.php?id=377'))
            ->addSource((new Source())->setUrl('http://anidb.net/perl-bin/animedb.pl?show=anime&aid=112'))
            ->addSource((new Source())->setUrl('http://www.allcinema.net/prog/show_c.php?num_c=163027'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/199/Sen_to_Chihiro_no_Kamikakushi'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/199/'))
            ->addSource((new Source())->setUrl('http://en.wikipedia.org/wiki/Spirited_Away'))
            ->addSource((new Source())->setUrl('http://ru.wikipedia.org/wiki/%D0%A3%D0%BD%D0%B5%D1%81%D1%91%D0%BD%D0%BD%D1%8B%D0%B5_%D0%BF%D1%80%D0%B8%D0%B7%D1%80%D0%B0%D0%BA%D0%B0%D0%BC%D0%B8'))
            ->addSource((new Source())->setUrl('http://ja.wikipedia.org/wiki/%E5%8D%83%E3%81%A8%E5%8D%83%E5%B0%8B%E3%81%AE%E7%A5%9E%E9%9A%A0%E3%81%97'))
            ->addSource((new Source())->setUrl('http://oboi.kards.ru/?act=search&level=6&search_str=Spirited%20Away'))
            ->addSource((new Source())->setUrl('http://www.fansubs.ru/base.php?id=368'))
            ->addSource((new Source())->setUrl('http://uanime.org.ua/anime/38.html'))
            ->addSource((new Source())->setUrl('http://www.world-art.ru/animation/animation.php?id=87'))
            ->addSource((new Source())->setUrl('http://shikimori.org/animes/199-sen-to-chihiro-no-kamikakushi'));

        if ($item->getName() != 'Spirited Away') {
            $item->addName((new Name())->setName('Spirited Away'));
        }

        return $item
            ->addName((new Name())->setName('Sen to Chihiro no Kamikakushi'))
            ->addName((new Name())->setName('千と千尋の神隠し'));
    }

    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\CatalogBundle\Service\Install\Item::setStorage()
     */
    public function setStorage(Storage $storage)
    {
        $this->getItem()->setPath($storage->getPath().'Spirited Away (2001)'.DIRECTORY_SEPARATOR);
        return parent::setStorage($storage);
    }
}
