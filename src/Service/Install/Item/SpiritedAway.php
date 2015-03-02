<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Service\Install\Item;

use AnimeDb\Bundle\CatalogBundle\Service\Install\Item;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;
use AnimeDb\Bundle\CatalogBundle\Entity\Source;
use AnimeDb\Bundle\CatalogBundle\Entity\Name;

/**
 * Install item
 *
 * @package AnimeDb\Bundle\CatalogBundle\Service\Install\Item
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
        return parent::buildItem()
            ->setCountry($this->getCountry('JP'))
            ->setCover('samples/spirited-away.jpg')
            ->setDatePremiere(new \DateTime('2001-07-20'))
            ->setDuration(125)
            ->setEpisodesNumber('1')
            ->setStudio($this->getStudio('Studio Ghibli'))
            ->setType($this->getType('feature'))
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

    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\CatalogBundle\Service\Install\Item::setLocale()
     */
    public function setLocale($locale)
    {
        // installing the language-specific data
        if (substr($locale, 0, 2) == 'ru') {
            $this->getItem()
                ->setSummary(
                    'Маленькая Тихиро вместе с мамой и папой переезжают в новый дом. Заблудившись по дороге, они '
                    .'оказываются в странном пустынном городе, где их ждет великолепный пир. Родители с жадностью '
                    .'набрасываются на еду и к ужасу девочки превращаются в свиней, став пленниками злой колдуньи '
                    .'Юбабы, властительницы таинственного мира древних богов и могущественных духов. Теперь, '
                    .'оказавшись одна среди магических существ и загадочных видений, отважная Тихиро должна '
                    .'придумать, как избавить своих родителей от чар коварной старухи и спастись из пугающего '
                    .'царства призраков...'
                );
        } else {
            $this->getItem()
                ->setSummary(
                    'On the way to their new home, 10-year-old Chihiro Ogino\'s family stumbles upon a deserted theme '
                    .'park. Intrigued, the family investigates the park, though unbeknownst to them, it is secretly '
                    .'inhabited by spirits who sleep by day and appear at night. When Chihiro\'s mother and father '
                    .'eat food from a restaurant in the street, angry spirits turn them into pigs. Furthermore, a '
                    .'wide sea has appeared between the spirit world and the human one, trapping Chihiro, the sole '
                    .'human, in a land of spirits. Luckily for her though, a mysterious boy named Haku appears, '
                    .'claiming to know her from the past. Under his instructions, Chihiro secures a job in the '
                    .'bathhouse where Haku works. With only her courage and some new found friends to aid her, '
                    .'Chihiro embarks on a journey to turn her parents back to their original forms and return home.'
                );
        }

        // set names from locale
        $this->getItem()->setName($this->getNameForLocale($locale));

        if ($this->getItem()->getName() != 'Spirited Away') {
            $this->getItem()->addName((new Name())->setName('Spirited Away'));
        }

        $this->getItem()
            ->addName((new Name())->setName('Sen to Chihiro no Kamikakushi'))
            ->addName((new Name())->setName('千と千尋の神隠し'));

        return parent::setLocale($locale);
    }

    /**
     * Get name for locale
     *
     * @param string $locale
     *
     * @return string
     */
    protected function getNameForLocale($locale)
    {
        switch (substr($locale, 0, 2)) {
            case 'ru':
                return 'Унесённые призраками';
            case 'pt':
                return 'A Viagem de Chihiro';
            case 'hr':
                return 'Avanture male Chihiro';
            case 'no':
            case 'da':
                return 'Chihiro Og Heksene';
            case 'de':
                return 'Chihiros Reise ins Zauberland';
            case 'es':
                return 'El Viaje de Chihiro';
            case 'fi':
                return 'Henkien kätkemä';
            case 'it':
                return 'La Città Incantata';
            case 'fr':
                return 'Le voyage de Chihiro';
            case 'tr':
                return 'Ruhların Kaçışı';
            case 'pl':
                return 'Spirited Away: W krainie bogów';
            case 'lt':
                return 'Stebuklingi Šihiros nuotykiai Dvasių pasaulyje';
            case 'et':
                return 'Vaimudest viidud';
            case 'he':
                return 'המסע המופלא';
            case 'ar':
                return 'شيهيرو';
            case 'cs':
                return 'Cesta do fantazie';
            case 'sk':
                return 'Cesta do fantázie';
            case 'hu':
                return 'Chihiro Szellemországban';
            case 'nl':
                return 'De reis van Chihiro';
            case 'lv':
                return 'Gariem līdzi';
            case 'sr':
                return 'Začarani grad';
            case 'sl':
                return 'Čudežno potovanje';
            case 'el':
                return 'Ταξίδι στη Χώρα των Θαυμάτων';
            case 'bg':
                return 'Отнесени от Духове';
            case 'zh':
                return '千與千尋';
            case 'ko':
                return '센과 치히로의 행방불명';
            default:
                return 'Spirited Away';
        }
    }
}
