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
class SamuraiChamploo extends Item
{
    /**
     * @return ItemEntity
     */
    protected function buildItem()
    {
        $item = parent::buildItem()
            ->setCountry($this->getCountry('JP'))
            ->setCover('samples/samurai-champloo.jpg')
            ->setDatePremiere(new \DateTime('2004-05-20'))
            ->setDateEnd(new \DateTime('2005-03-19'))
            ->setDuration(25)
            ->setEpisodes($this->translator->trans(
'1. Tempestuous Temperaments (20.05.2004, 25 min.)
2. Redeye Reprisal (03.06.2004, 25 min.)
3. Hellhounds for Hire (Part 1) (10.06.2004, 25 min.)
4. Hellhounds for Hire (Part 2) (17.06.2004, 25 min.)
5. Artistic Anarchy (24.06.2004, 25 min.)
6. Stranger Searching (01.07.2004, 25 min.)
7. A Risky Racket (08.07.2004, 25 min.)
8. The Art of Altercation (15.07.2004, 25 min.)
9. Beatbox Bandits (22.07.2004, 25 min.)
10. Lethal Lunacy (29.07.2004, 25 min.)
11. Gamblers and Gallantry (05.08.2004, 25 min.)
12. The Disorder Diaries (12.08.2004, 25 min.)
13. Misguided Miscreants (Part 1) (26.08.2004, 25 min.)
14. Misguided Miscreants (Part 2) (02.09.2004, 25 min.)
15. Bogus Booty (09.09.2004, 25 min.)
16. Lullabies of The Lost (Verse 1) (16.09.2004, 25 min.)
17. Lullabies of The Lost (Verse 2) (23.09.2004, 25 min.)
18. War of The Words (22.01.2005, 25 min.)
19. Unholy Union (29.01.2005, 25 min.)
20. Elegy of Entrapment (Verse 1) (05.02.2005, 25 min.)
21. Elegy of Entrapment (Verse 2) (12.02.2005, 25 min.)
22. Cosmic Collisions (19.02.2005, 25 min.)
23. Baseball Blues (26.02.2005, 25 min.)
24. Evanescent Encounter (Part 1) (05.03.2005, 25 min.)
25. Evanescent Encounter (Part 2) (12.03.2005, 25 min.)
26. Evanescent Encounter (Part 3) (19.03.2005, 25 min.)',
                [],
                'item'
            ))
            ->setEpisodesNumber('26')
            ->setName($this->translator->trans('Samurai Champloo', [], 'item'))
            ->setStudio($this->getStudio('Manglobe'))
            ->setType($this->getType('tv'))
            ->setSummary($this->translator->trans(
                'Mugen\'s a buck-wild warrior — violent, thoughtless and womanising. Jin is a vagrant ronin — '
                .'mysterious, traditional, well-mannered and very strong as well. These two fiercely independent '
                .'warriors cannot be any more different from one another, yet their paths cross when Fuu, a ditzy '
                .'waitress, saves them from being executed when they are arrested after a violent sword fight. Fuu '
                .'convinces the two vagrant young men to help her find a mysterious samurai "who smells of '
                .'sunflowers". And so their journey begins...',
                [],
                'item'
            ))
            ->addGenre($this->getGenre('Comedy'))
            ->addGenre($this->getGenre('Drama'))
            ->addGenre($this->getGenre('Samurai'))
            ->addGenre($this->getGenre('Adventure'))
            ->addSource((new Source())->setUrl('http://anidb.net/perl-bin/animedb.pl?show=anime&aid=1543'))
            ->addSource((new Source())->setUrl('http://cal.syoboi.jp/tid/395/time'))
            ->addSource((new Source())->setUrl('http://en.wikipedia.org/wiki/Samurai_Champloo'))
            ->addSource((new Source())->setUrl('http://ja.wikipedia.org/wiki/%E3%82%B5%E3%83%A0%E3%83%A9%E3%82%A4%E3%83%81%E3%83%A3%E3%83%B3%E3%83%97%E3%83%AB%E3%83%BC'))
            ->addSource((new Source())->setUrl('http://ru.wikipedia.org/wiki/%D0%A1%D0%B0%D0%BC%D1%83%D1%80%D0%B0%D0%B9_%D0%A7%D0%B0%D0%BC%D0%BF%D0%BB%D1%83'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/205/'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/205/Samurai_Champloo'))
            ->addSource((new Source())->setUrl('http://wiki.livedoor.jp/radioi_34/d/%a5%b5%a5%e0%a5%e9%a5%a4%a5%c1%a5%e3%a5%f3%a5%d7%a5%eb%a1%bc'))
            ->addSource((new Source())->setUrl('http://www.allcinema.net/prog/show_c.php?num_c=319278'))
            ->addSource((new Source())->setUrl('http://www.animenewsnetwork.com/encyclopedia/anime.php?id=2636'))
            ->addSource((new Source())->setUrl('http://www.fansubs.ru/base.php?id=361'))
            ->addSource((new Source())->setUrl('http://www1.vecceed.ne.jp/~m-satomi/SAMURAICHANPLOO.html'))
            ->addSource((new Source())->setUrl('http://www.world-art.ru/animation/animation.php?id=2699'))
            ->addSource((new Source())->setUrl('http://shikimori.org/animes/205-samurai-champloo'));

        if ($item->getName() != 'Samurai Champloo') {
            $item->addName((new Name())->setName('Samurai Champloo'));
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
        $this->getItem()->setPath($storage->getPath().'Samurai Champloo (2004) [TV]'.DIRECTORY_SEPARATOR);

        return parent::setStorage($storage);
    }
}
