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
class SamuraiXTrustBetrayal extends Item
{
    /**
     * @return ItemEntity
     */
    protected function buildItem()
    {
        $item = parent::buildItem()
            ->setCountry($this->getCountry('JP'))
            ->setCover('samples/samurai-x-trust-betrayal.jpg')
            ->setDatePremiere(new \DateTime('1999-02-20'))
            ->setDateEnd(new \DateTime('1999-09-22'))
            ->setDuration(30)
            ->setEpisodes($this->translator->trans(
'1. The Man of the Slashing Sword (20.02.1999, 30 min.)
2. The Lost Cat (21.04.1999, 30 min.)
3. The Previous Night at the Mountain Home (19.06.1999, 30 min.)
4. The Cross-Shaped Wound (22.09.1999, 30 min.)',
                [],
                'item'
            ))
            ->setEpisodesNumber('4')
            ->setName($this->translator->trans('Rurouni Kenshin: Meiji Kenkaku Romantan - Tsuioku Hen', [], 'item'))
            ->setStudio($this->getStudio('Studio DEEN'))
            ->setType($this->getType('ova'))
            ->setSummary($this->translator->trans(
                'Taken by slavers when he was a child, Himura Kenshin is rescued only when an encounter with bandits '
                .'kills off everyone but him. He is found by a master of the Divine Justice School of Swordmanship, a '
                .'school so deadly that to train in it, means death for either the master or student, there can only '
                .'be one master. Taken by the master, Kenshin is trained in this school, only to leave before '
                .'finishing so that he may join the Meiji restoration and help prevent further tragedies like his own.'
                ."\n\n"
                .'Thus is born the Battousai, the greatest strength of the Ishin Patriots, a boy of 15 who kills for '
                .'the sake of building a new, better world. One night, he comes across a mysterious woman, Tomoe. He '
                .'must hide with her when the revolution stumbles. They marry for appearances, but soon fall in love. '
                .'Tomoe has another reason to be with Kenshin, one she regrets but cannot stop. Revenge must be '
                .'satisfied, and only blood can do that.',
                [],
                'item'
            ))
            ->addGenre($this->getGenre('Drama'))
            ->addGenre($this->getGenre('Samurai'))
            ->addGenre($this->getGenre('Romance'))
            ->addSource((new Source())->setUrl('http://anidb.net/perl-bin/animedb.pl?show=anime&aid=73'))
            ->addSource((new Source())->setUrl('http://en.wikipedia.org/wiki/Rurouni_Kenshin'))
            ->addSource((new Source())->setUrl('http://ja.wikipedia.org/wiki/%E3%82%8B%E3%82%8D%E3%81%86%E3%81%AB%E5%89%A3%E5%BF%83_-%E6%98%8E%E6%B2%BB%E5%89%A3%E5%AE%A2%E6%B5%AA%E6%BC%AB%E8%AD%9A-'))
            ->addSource((new Source())->setUrl('http://ru.wikipedia.org/wiki/%D0%A1%D0%B0%D0%BC%D1%83%D1%80%D0%B0%D0%B9_X'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/44/'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/44/Rurouni_Kenshin:_Meiji_Kenkaku_Romantan_-_Tsuiokuhen'))
            ->addSource((new Source())->setUrl('http://www.allcinema.net/prog/show_c.php?num_c=88146'))
            ->addSource((new Source())->setUrl('http://www.animenewsnetwork.com/encyclopedia/anime.php?id=210'))
            ->addSource((new Source())->setUrl('http://www.fansubs.ru/base.php?id=870'))
            ->addSource((new Source())->setUrl('http://www.world-art.ru/animation/animation.php?id=82'))
            ->addSource((new Source())->setUrl('http://shikimori.org/animes/44-rurouni-kenshin-meiji-kenkaku-romantan-tsuiokuhen'));

        if ($item->getName() != 'Rurouni Kenshin: Meiji Kenkaku Romantan - Tsuioku Hen') {
            $item->addName((new Name())->setName('Rurouni Kenshin: Meiji Kenkaku Romantan - Tsuioku Hen'));
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
        $this->getItem()->setPath($storage->getPath().'Samurai X - Trust Betrayal (1999) [OVA]'.DIRECTORY_SEPARATOR);

        return parent::setStorage($storage);
    }
}
