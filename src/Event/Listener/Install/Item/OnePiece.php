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
class OnePiece extends Item
{
    /**
     * @return ItemEntity
     */
    protected function buildItem()
    {
        $item = parent::buildItem()
            ->setCountry($this->getCountry('JP'))
            ->setCover('samples/one-piece.jpg')
            ->setDatePremiere(new \DateTime('1999-10-20'))
            ->setDuration(25)
            ->setEpisodesNumber('669+')
            ->setFileInfo($this->translator->trans('+ 6 specials', [], 'item'))
            ->setName($this->translator->trans('One Piece', [], 'item'))
            ->setStudio($this->getStudio('Toei Animation'))
            ->setType($this->getType('tv'))
            ->setSummary($this->translator->trans(
                'Gol D. Roger was known as the Pirate King, the strongest and most infamous being to have sailed '
                .'the Grand Line. The capture and death of Roger by the World Government brought a change '
                .'throughout the world. His last words before his death revealed the location of the greatest '
                .'treasure in the world, One Piece. It was this revelation that brought about the Grand Age of '
                .'Pirates, men who dreamed of finding One Piece (which promises an unlimited amount of riches and '
                .'fame), and quite possibly the most coveted of titles for the person who found it, the title of '
                .'the Pirate King.'
                .PHP_EOL.PHP_EOL
                .'Enter Monkey D. Luffy, a 17-year-old boy that defies your standard definition of a pirate. '
                .'Rather than the popular persona of a wicked, hardened, toothless pirate who ransacks villages '
                .'for fun, Luffy’s reason for being a pirate is one of pure wonder; the thought of an exciting '
                .'adventure and meeting new and intriguing people, along with finding One Piece, are his reasons '
                .'of becoming a pirate. Following in the footsteps of his childhood hero, Luffy and his crew '
                .'travel across the Grand Line, experiencing crazy adventures, unveiling dark mysteries and '
                .'battling strong enemies, all in order to reach One Piece.',
                [],
                'item'
            ))
            ->addGenre($this->getGenre('Adventure'))
            ->addGenre($this->getGenre('Comedy'))
            ->addGenre($this->getGenre('Shounen'))
            ->addGenre($this->getGenre('Fantasy'))
            ->addSource((new Source())->setUrl('http://www.animenewsnetwork.com/encyclopedia/anime.php?id=836'))
            ->addSource((new Source())->setUrl('http://anidb.net/perl-bin/animedb.pl?show=anime&aid=69'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/21/One_Piece'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/21/'))
            ->addSource((new Source())->setUrl('http://cal.syoboi.jp/tid/350/time'))
            ->addSource((new Source())->setUrl('http://www.allcinema.net/prog/show_c.php?num_c=162790'))
            ->addSource((new Source())->setUrl('http://en.wikipedia.org/wiki/One_Piece'))
            ->addSource((new Source())->setUrl('http://ru.wikipedia.org/wiki/One_Piece'))
            ->addSource((new Source())->setUrl('http://ja.wikipedia.org/wiki/ONE_PIECE_%28%E3%82%A2%E3%83%8B%E3%83%A1%29'))
            ->addSource((new Source())->setUrl('http://www.fansubs.ru/base.php?id=731'))
            ->addSource((new Source())->setUrl('http://www.world-art.ru/animation/animation.php?id=803'))
            ->addSource((new Source())->setUrl('http://shikimori.org/animes/21-one-piece'));

        // installing the language-specific data
        switch (substr($this->translator->getLocale(), 0, 2)) {
            case 'ua':
                $item->addName((new Name())->setName('Большой куш'));
                // no break
            case 'ru':
                $item->addName((new Name())->setName('Ван-Пис'));
        }

        if ($item->getName() != 'One Piece') {
            $item->addName((new Name())->setName('One Piece'));
        }

        return $item->addName((new Name())->setName('ワンピース'));
    }

    /**
     * @param Storage $storage
     *
     * @return Item
     */
    public function setStorage(Storage $storage)
    {
        $this->getItem()->setPath($storage->getPath().'One Piece (2011) [TV]'.DIRECTORY_SEPARATOR);
        return parent::setStorage($storage);
    }
}
