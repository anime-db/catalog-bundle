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
class Beck extends Item
{
    /**
     * @return ItemEntity
     */
    protected function buildItem()
    {
        $item = parent::buildItem()
            ->setCountry($this->getCountry('JP'))
            ->setCover('samples/beck.jpg')
            ->setDatePremiere(new \DateTime('2004-10-07'))
            ->setDateEnd(new \DateTime('2005-03-31'))
            ->setDuration(25)
            ->setEpisodes($this->translator->trans(
'1. The View at Fourteen (07.10.2004, 25 min.)
2. Live House (14.10.2004, 25 min.)
3. Moon on the Water (21.10.2004, 25 min.)
4. Strum the Guitar (28.10.2004, 25 min.)
5. Beck (04.11.2004, 25 min.)
6. Hyodo and the Jaguar (11.11.2004, 25 min.)
7. Prudence (18.11.2004, 25 min.)
8. Broadcast in the School (25.11.2004, 25 min.)
9. The Night Before Live (02.12.2004, 25 min.)
10. Face (09.12.2004, 25 min.)
11. Summer Holiday (16.12.2004, 25 min.)
12. Secret Live (23.12.2004, 25 min.)
13. Ciel Bleu (30.12.2004, 25 min.)
14. Dream (06.01.2005, 25 min.)
15. Back to School (13.01.2005, 25 min.)
16. Indies (20.01.2005, 25 min.)
17. Three Days (27.01.2005, 25 min.)
18. Leon Sykes (03.02.2005, 25 min.)
19. Blues (10.02.2005, 25 min.)
20. Greatful Sound (17.02.2005, 25 min.)
21. Write Music (24.02.2005, 25 min.)
22. Night Before the Festival (03.03.2005, 25 min.)
23. Festival (10.03.2005, 25 min.)
24. Third Stage (17.03.2005, 25 min.)
25. Slip Out (24.03.2005, 25 min.)
26. America (31.03.2005, 25 min.)',
                [],
                'item'
            ))
            ->setEpisodesNumber('26')
            ->setName($this->translator->trans('Beck', [], 'item'))
            ->setStudio($this->getStudio('Madhouse'))
            ->setType($this->getType('tv'))
            ->setSummary($this->translator->trans(
                'Tanaka Yukio, better known by his nickname Koyuki is a 14 year old who feels disconnected from life '
                .'in general. Through the act of saving a mismatched dog, he meets guitarist Minami Ryuusuke, and '
                .'becomes involved in Ryuusuke\'s new band BECK. Koyuki\'s life starts to change as the band '
                .'struggles towards fame.',
                [],
                'item'
            ))
            ->addGenre($this->getGenre('Drama'))
            ->addGenre($this->getGenre('Music'))
            ->addGenre($this->getGenre('Romance'))
            ->addSource((new Source())->setUrl('http://anidb.net/perl-bin/animedb.pl?show=anime&aid=2320'))
            ->addSource((new Source())->setUrl('http://cal.syoboi.jp/tid/490/time'))
            ->addSource((new Source())->setUrl('http://en.wikipedia.org/wiki/BECK:_Mongolian_Chop_Squad'))
            ->addSource((new Source())->setUrl('http://ja.wikipedia.org/wiki/BECK_%28%E6%BC%AB%E7%94%BB%29'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/57/'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/57/Beck'))
            ->addSource((new Source())->setUrl('http://ru.wikipedia.org/wiki/BECK:_Mongolian_Chop_Squad'))
            ->addSource((new Source())->setUrl('http://www.allcinema.net/prog/show_c.php?num_c=321252'))
            ->addSource((new Source())->setUrl('http://www.animenewsnetwork.com/encyclopedia/anime.php?id=4404'))
            ->addSource((new Source())->setUrl('http://www.fansubs.ru/base.php?id=725'))
            ->addSource((new Source())->setUrl('http://www.world-art.ru/animation/animation.php?id=2671'))
            ->addSource((new Source())->setUrl('http://shikimori.org/animes/57-beck'));

        // installing the language-specific data
        switch (substr($this->translator->getLocale(), 0, 2)) {
            case 'ua':
            case 'ru':
                $item->addName((new Name())->setName('Бек: Восточная Ударная Группа'));
        }

        if ($item->getName() != 'Beck') {
            $item->addName((new Name())->setName('Beck'));
        }
        if ($item->getName() != 'Beck - Mongorian Chop Squad') {
            $item->addName((new Name())->setName('Beck - Mongorian Chop Squad'));
        }

        return $item
            ->addName((new Name())->setName('Beck: Mongolian Chop Squad'))
            ->addName((new Name())->setName('Beck Mongolian Chop Squad'));
    }

    /**
     * @param Storage $storage
     *
     * @return Item
     */
    public function setStorage(Storage $storage)
    {
        $this->getItem()->setPath($storage->getPath().'Beck (2004) [TV]'.DIRECTORY_SEPARATOR);
        return parent::setStorage($storage);
    }
}
