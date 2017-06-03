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
class TengenToppaGurrenLagann extends Item
{
    /**
     * @return ItemEntity
     */
    protected function buildItem()
    {
        $item = parent::buildItem()
            ->setCountry($this->getCountry('JP'))
            ->setCover('samples/tengen-toppa-gurren-lagann.jpg')
            ->setDateEnd(new \DateTime('2007-04-01'))
            ->setDatePremiere(new \DateTime('2007-09-30'))
            ->setDuration(25)
            ->setEpisodes($this->translator->trans(
'1. Pierce the Heavens with Your Drill! (01.04.2007, 25 min.)
2. I Said I`d Ride It (08.04.2007, 25 min.)
3. You Two-Faced Son of a Bitch! (15.04.2007, 25 min.)
4. Does Having So Many Faces Make You Great? (22.04.2007, 25 min.)
5. I Don`t Understand It At All! (29.04.2007, 25 min.)
6. All of You Bastards Put Us In Hot Water! (06.05.2007, 25 min.)
7. You`ll Be the One To Do That! (13.05.2007, 25 min.)
8. Farewell Comrades! (20.05.2007, 25 min.)
9. Just What Exactly Is a Human? (27.05.2007, 25 min.)
10. Who Really Was Your Big Brother? (03.06.2007, 25 min.)
11. Simon, Please Remove Your Hand (10.06.2007, 25 min.)
12. Youko-san, I Have Something to Ask of You (17.06.2007, 25 min.)
13. Everybody, Eat to Your Heart`s Content (24.06.2007, 25 min.)
14. How Are You, Everyone? (01.07.2007, 25 min.)
15. I`ll Head Towards Tomorrow (08.07.2007, 25 min.)
16. Summary Episode (15.07.2007, 25 min.)
17. You Understand Nothing (22.07.2007, 25 min.)
18. I`ll Make You Tell the Truth of the World (29.07.2007, 25 min.)
19. We Must Survive. No Matter What it Takes! (05.08.2007, 25 min.)
20. Oh God, To How Far Will You Test Us? (12.08.2007, 25 min.)
21. You Must Survive (19.08.2007, 25 min.)
22. And to Space (26.08.2007, 25 min.)
23. Let`s Go, The Final Battle (02.09.2007, 25 min.)
24. We Will Never Forget, This Minute and Second (09.09.2007, 25 min.)
25. I Accept Your Dying Wish! (16.09.2007, 25 min.)
26. Let`s Go, Comrades! (23.09.2007, 25 min.)
27. All the Lights in the Sky are Stars (30.09.2007, 25 min.)',
                [],
                'item'
            ))
            ->setEpisodesNumber('27')
            ->setFileInfo($this->translator->trans('+ 2 specials', [], 'item'))
            ->setName($this->translator->trans('Tengen Toppa Gurren Lagann', [], 'item'))
            ->setStudio($this->getStudio('Gainax'))
            ->setType($this->getType('tv'))
            ->setSummary($this->translator->trans(
                'In the distant future, people build their homes and raise domestic animals in subterranean caverns '
                .'called "pits". This way of life has persisted for hundreds of years, and yet, the people are still '
                .'powerless against the earthquakes and cave-ins which occasionally devastate their villages. Jeeha '
                .'is one such village...'
                ."\n\n"
                .'One day, while digging tunnels to expand Jeeha\'s edges, a boy named Simon happens to find a small, '
                .'shiny drill. Meanwhile, Kamina — Simon\'s soon-to-be "bro" — insists that there is another land '
                .'above the village and tries to break through the ceiling in order to leave the cavern. He fails; '
                .'however, an earthquake suddenly occurs, the ceiling collapses, and a gigantic robot — a "Ganmen" — '
                .'crashes to the cave floor!'
                ."\n\n"
                .'Kamina is now certain that there is a world above their tiny pit-village. Encouraged, he begins to '
                .'fight recklessly against the robot, until he encounters another person who has arrived from the '
                .'land above. She is a beautiful girl by the name of Youko, who has come from a neighbouring village '
                .'with a rifle in hand. She has been hunting the robot, but her shots only seem to irritate it. '
                .'Desperately trying to escape its attacks, Simon brings Kamina and Youko to what he had previously '
                .'found: A mysterious robot with a head for a body.'
                ."\n\n"
                .'And so begins the "epic" adventure...',
                [],
                'item'
            ))
            ->addGenre($this->getGenre('Drama'))
            ->addGenre($this->getGenre('Adventure'))
            ->addGenre($this->getGenre('Mecha'))
            ->addGenre($this->getGenre('Sci-fi'))
            ->addSource((new Source())->setUrl('http://anidb.net/perl-bin/animedb.pl?show=anime&aid=4575'))
            ->addSource((new Source())->setUrl('http://cal.syoboi.jp/tid/1000/time'))
            ->addSource((new Source())->setUrl('http://en.wikipedia.org/wiki/Tengen_Toppa_Gurren_Lagann'))
            ->addSource((new Source())->setUrl('http://ja.wikipedia.org/wiki/%E5%A4%A9%E5%85%83%E7%AA%81%E7%A0%B4%E3%82%B0%E3%83%AC%E3%83%B3%E3%83%A9%E3%82%AC%E3%83%B3'))
            ->addSource((new Source())->setUrl('http://ru.wikipedia.org/wiki/Tengen_Toppa_Gurren_Lagann'))
            ->addSource((new Source())->setUrl('http://www.allcinema.net/prog/show_c.php?num_c=326669'))
            ->addSource((new Source())->setUrl('http://www.animenewsnetwork.com/encyclopedia/anime.php?id=6698'))
            ->addSource((new Source())->setUrl('http://www.fansubs.ru/base.php?id=1769'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/2001/'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/2001/Tengen_Toppa_Gurren_Lagann'))
            ->addSource((new Source())->setUrl('http://www.world-art.ru/animation/animation.php?id=5959'))
            ->addSource((new Source())->setUrl('http://shikimori.org/animes/2001-tengen-toppa-gurren-lagann'));

        if ($item->getName() != 'Tengen Toppa Gurren Lagann') {
            $item->addName((new Name())->setName('Tengen Toppa Gurren Lagann'));
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
        $this->getItem()->setPath($storage->getPath().'Tengen Toppa Gurren Lagann (2007) [TV]'.DIRECTORY_SEPARATOR);

        return parent::setStorage($storage);
    }
}
