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
class OnePiece extends Item
{
    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\CatalogBundle\Service\Install\Item::buildItem()
     */
    protected function buildItem()
    {
        return parent::buildItem()
            ->setCountry($this->getCountry('JP'))
            ->setCover('samples/one-piece.jpg')
            ->setDatePremiere(new \DateTime('1999-10-20'))
            ->setDuration(25)
            ->setEpisodesNumber('669+')
            ->setStudio($this->getStudio('Toei Animation'))
            ->setType($this->getType('tv'))
            ->addGenre($this->getGenre('Adventure'))
            ->addGenre($this->getGenre('Comedy'))
            ->addGenre($this->getGenre('Shounen'))
            ->addGenre($this->getGenre('Fantasy'))
            ->addSource((new Source())->setUrl('http://www.animenewsnetwork.com/encyclopedia/anime.php?id=836'))
            ->addSource((new Source())->setUrl('http://anidb.net/perl-bin/animedb.pl?show=anime&aid=69'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/21/One_Piece'))
            ->addSource((new Source())->setUrl('http://cal.syoboi.jp/tid/350/time'))
            ->addSource((new Source())->setUrl('http://www.allcinema.net/prog/show_c.php?num_c=162790'))
            ->addSource((new Source())->setUrl('http://en.wikipedia.org/wiki/One_Piece'))
            ->addSource((new Source())->setUrl('http://ru.wikipedia.org/wiki/One_Piece'))
            ->addSource((new Source())->setUrl('http://ja.wikipedia.org/wiki/ONE_PIECE_%28%E3%82%A2%E3%83%8B%E3%83%A1%29'))
            ->addSource((new Source())->setUrl('http://www.fansubs.ru/base.php?id=731'))
            ->addSource((new Source())->setUrl('http://www.world-art.ru/animation/animation.php?id=803'))
            ->addSource((new Source())->setUrl('http://shikimori.org/animes/21-one-piece'));
    }

    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\CatalogBundle\Service\Install\Item::setStorage()
     */
    public function setStorage($storage)
    {
        $this->getItem()->setPath($storage->getPath().'One Piece (2011) [TV]'.DIRECTORY_SEPARATOR);
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
                ->setFileInfo('+ 6 спэшлов')
                ->setSummary(
                    'Последние слова, произнесенные Королем Пиратов перед казнью, вдохновили многих: «Мои сокровища? '
                    .'Коли хотите, забирайте. Ищите – я их все оставил там!». Легендарная фраза Золотого Роджера '
                    .'ознаменовала начало Великой Эры Пиратов – тысячи людей в погоне за своими мечтами отправились '
                    .'на Гранд Лайн, самое опасное место в мире, желая стать обладателями мифических сокровищ... Но с '
                    .'каждым годом романтиков становилось все меньше, их постепенно вытесняли прагматичные '
                    .'пираты-разбойники, которым награбленное добро было куда ближе, чем какие-то «никчемные мечты». '
                    .'Но вот, одним прекрасным днем, семнадцатилетний Монки Д. Луффи исполнил заветную мечту '
                    .'детства - отправился в море. Его цель - ни много, ни мало стать новым Королем Пиратов. За '
                    .'достаточно короткий срок юному капитану удается собрать команду, состоящую из не менее '
                    .'амбициозных искателей приключений. И пусть ими движут совершенно разные устремления, главное, '
                    .'этим ребятам важны не столько деньги и слава, сколько куда более ценное – принципы и верность '
                    .'друзьям. И еще – служение Мечте. Что ж, пока по Гранд Лайн плавают такие люди, Великая Эра '
                    .'Пиратов всегда будет с нами!'
                );
        } else {
            $this->getItem()
                ->setFileInfo('+ 6 specials')
                ->setSummary(
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
                    .'battling strong enemies, all in order to reach One Piece.'
                );
        }

        // set names from locale
        switch (substr($locale, 0, 2)) {
            case 'ru':
                $this->getItem()
                    ->setName('Большой куш')
                    ->addName((new Name())->setName('Ван-Пис'));
                break;
            case 'ua':
                $this->getItem()
                    ->setName('Великий куш')
                    ->addName((new Name())->setName('Большой куш'))
                    ->addName((new Name())->setName('Ван-Пис'));
                break;
            case 'it':
                $this->getItem()->setName('All`arrembaggio!');
                break;
            case 'el':
                $this->getItem()->setName('Ντρέικ και το Κυνήγι του Θησαυρού');
                break;
            case 'he':
                $this->getItem()->setName('וואן פיס');
                break;
            case 'ar':
                $this->getItem()->setName('ون بيس');
                break;
            case 'th':
                $this->getItem()->setName('วันพีซ');
                break;
            case 'my':
                $this->getItem()->setName('Budak Getah');
                break;
            case 'fa':
                $this->getItem()->setName('وان پیس');
                break;
            case 'bd':
                $this->getItem()->setName('ওয়ান পিস্');
                break;
            case 'zh':
                $this->getItem()->setName('海贼王');
                break;
            case 'ko':
                $this->getItem()->setName('원피스');
                break;
            default:
                $this->getItem()->setName('One Piece');
        }

        if ($this->getItem()->getName() != 'One Piece') {
            $this->getItem()->addName((new Name())->setName('One Piece'));
        }

        $this->getItem()->addName((new Name())->setName('ワンピース'));

        return parent::setLocale($locale);
    }
}