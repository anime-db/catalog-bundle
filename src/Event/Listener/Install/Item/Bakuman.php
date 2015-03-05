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
class Bakuman extends Item
{
    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\CatalogBundle\Service\Install\Item::buildItem()
     */
    protected function buildItem()
    {
        $item = parent::buildItem()
            ->setCountry($this->getCountry('JP'))
            ->setCover('samples/bakuman.jpg')
            ->setDatePremiere(new \DateTime('2010-10-02'))
            ->setDateEnd(new \DateTime('2011-04-02'))
            ->setDuration(25)
            ->setEpisodes($this->translator->trans(
'1. Dream and Reality (02.10.2010, 25 min.)
2. Stupid and Clever (09.10.2010, 25 min.)
3. Parent and Child (16.10.2010, 25 min.)
4. Time and Key (23.10.2010, 25 min.)
5. Summer and Storyboard (30.10.2010, 25 min.)
6. Carrot and Stick (06.11.2010, 25 min.)
7. Tears and Tears (13.11.2010, 25 min.)
8. Anxiety and Anticipation (20.11.2010, 25 min.)
9. Regret and Consent (27.11.2010, 25 min.)
10. 10 and 2 (04.12.2010, 25 min.)
11. Chocolate and Next! (11.12.2010, 25 min.)
12. Feast and Graduation (18.12.2010, 25 min.)
13. Early Results And The Real Deal (25.12.2010, 25 min.)
14. Battles and Copying (08.01.2011, 25 min.)
15. Debut and Hectic (15.01.2011, 25 min.)
16. Wall and Kiss (22.01.2011, 25 min.)
17. Braggart and Kindness (29.01.2011, 25 min.)
18. Jealousy and Love (05.02.2011, 25 min.)
19. Two and One (12.02.2011, 25 min.)
20. Cooperation and Conditions (19.02.2011, 25 min.)
21. Literature and Music (26.02.2011, 25 min.)
22. Solidarity and Breakdown (05.03.2011, 25 min.)
23. Tuesday and Friday (19.03.2011, 25 min.)
24. Call and Eve (26.03.2011, 25 min.)
25. Yes and No (02.04.2011, 25 min.)',
                [],
                'item'
            ))
            ->setEpisodesNumber('25')
            ->setName($this->translator->trans('Bakuman.', [], 'item'))
            ->setStudio($this->getStudio('J.C.Staff'))
            ->setType($this->getType('tv'))
            ->setSummary($this->translator->trans(
                'Bakuman follows the story of high school student Mashiro Moritaka, a talented artist who does not '
                .'know what he wants to do with his future. One day he draws a picture of Azuki Miho, a girl he is '
                .'secretly fond of, during class and forgets the notebook at school. He comes back to find that his '
                .'classmate Takagi Akito is waiting for him with his notebook. Takagi tries to convince Mashiro to '
                .'become a mangaka, a manga artist, with him, only leading to Mashiro\'s disagreement. Mashiro goes '
                .'home and thinks about his mangaka uncle, who had only one successful series before he died in '
                .'obscurity. Mashiro is interrupted by a phone call from Takagi, who says that he is going to tell '
                .'Azuki that Mashiro likes her. Mashiro runs down to Azuki\'s house to find Takagi waiting for him. '
                .'Once Azuki comes out to meet them, Takagi tells her that he and Mashiro are aiming to be mangaka. '
                .'Mashiro then learns that she wants to be a seiyuu, a voice actor, and has shown promise in the '
                .'field. Mashiro, once again thinking about his uncle, accidentally proposes to Azuki who accepts. '
                .'However, she will only marry him after they achieve their dreams.',
                [],
                'item'
            ))
            ->addGenre($this->getGenre('Comedy'))
            ->addGenre($this->getGenre('Slice of life'))
            ->addSource((new Source())->setUrl('http://www.animenewsnetwork.com/encyclopedia/anime.php?id=11197'))
            ->addSource((new Source())->setUrl('http://anidb.net/perl-bin/animedb.pl?show=anime&aid=7251'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/7674/'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/7674/Bakuman.'))
            ->addSource((new Source())->setUrl('http://cal.syoboi.jp/tid/2037/time'))
            ->addSource((new Source())->setUrl('http://www.allcinema.net/prog/show_c.php?num_c=335759'))
            ->addSource((new Source())->setUrl('http://en.wikipedia.org/wiki/Bakuman'))
            ->addSource((new Source())->setUrl('http://ru.wikipedia.org/wiki/Bakuman'))
            ->addSource((new Source())->setUrl('http://ja.wikipedia.org/wiki/%E3%83%90%E3%82%AF%E3%83%9E%E3%83%B3%E3%80%82_%28%E3%82%A2%E3%83%8B%E3%83%A1%29'))
            ->addSource((new Source())->setUrl('http://www.fansubs.ru/base.php?id=3109'))
            ->addSource((new Source())->setUrl('http://www.world-art.ru/animation/animation.php?id=7740'))
            ->addSource((new Source())->setUrl('http://shikimori.org/animes/7674-bakuman'));

        if ($item->getName() != 'Bakuman.') {
            $item->addName((new Name())->setName('Bakuman.'));
        }

        return $item
            ->addName((new Name())->setName('Bakuman'))
            ->addName((new Name())->setName('爆漫王'))
            ->addName((new Name())->setName('食梦者'));
    }

    /**
     * (non-PHPdoc)
     * @see \AnimeDb\Bundle\CatalogBundle\Service\Install\Item::setStorage()
     */
    public function setStorage(Storage $storage)
    {
        $this->getItem()->setPath($storage->getPath().'Bakuman. (2010) [TV-1]'.DIRECTORY_SEPARATOR);
        return parent::setStorage($storage);
    }
}
