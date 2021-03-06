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
class FullmetalAlchemist extends Item
{
    /**
     * @return ItemEntity
     */
    protected function buildItem()
    {
        $item = parent::buildItem()
            ->setCountry($this->getCountry('JP'))
            ->setCover('samples/fullmetal-alchemist.jpg')
            ->setDateEnd(new \DateTime('2004-10-02'))
            ->setDatePremiere(new \DateTime('2003-10-04'))
            ->setDuration(25)
            ->setEpisodes($this->translator->trans(
'1. To Challenge the Sun (04.10.2003, 25 min.)
2. Body of the Sanctioned (11.10.2003, 25 min.)
3. Mother (18.10.2003, 25 min.)
4. A Forger`s Love (25.10.2003, 25 min.)
5. The Man with the Mechanical Arm (01.11.2003, 25 min.)
6. The Alchemy Exam (08.11.2003, 25 min.)
7. Night of the Chimera`s Cry (15.11.2003, 25 min.)
8. The Philosopher`s Stone (22.11.2003, 25 min.)
9. Be Thou for the People (29.11.2003, 25 min.)
10. The Phantom Thief (06.12.2003, 25 min.)
11. The Other Brothers Elric, Part 1 (13.12.2003, 25 min.)
12. The Other Brothers Elric, Part 2 (20.12.2003, 25 min.)
13. Fullmetal vs. Flame (27.12.2003, 25 min.)
14. Destruction`s Right Hand (10.01.2004, 25 min.)
15. The Ishbal Massacre (17.01.2004, 25 min.)
16. That Which Is Lost (24.01.2004, 25 min.)
17. House of the Waiting Family (31.01.2004, 25 min.)
18. Marcoh`s Notes (07.02.2004, 25 min.)
19. The Truth Behind Truths (14.02.2004, 25 min.)
20. Soul of the Guardian (21.02.2004, 25 min.)
21. The Red Glow (28.02.2004, 25 min.)
22. Created Human (06.03.2004, 25 min.)
23. Fullmetal Heart (13.03.2004, 25 min.)
24. Bonding Memories (20.03.2004, 25 min.)
25. Words of Farewell (27.03.2004, 25 min.)
26. Her Reason (03.04.2004, 25 min.)
27. Teacher (10.04.2004, 25 min.)
28. All is One, One is All (17.04.2004, 25 min.)
29. The Untainted Child (24.04.2004, 25 min.)
30. Assault on South Headquarters (01.05.2004, 25 min.)
31. Sin (08.05.2004, 25 min.)
32. Dante of the Deep Forest (15.05.2004, 25 min.)
33. Al, Captured (29.05.2004, 25 min.)
34. Theory of Avarice (05.06.2004, 25 min.)
35. Reunion of the Fallen (12.06.2004, 25 min.)
36. The Sinner Within (19.06.2004, 25 min.)
37. The Flame Alchemist, the Bachelor Lieutenant and the Mystery of Warehouse 13 (26.06.2004, 25 min.)
38. With the River`s Flow (03.07.2004, 25 min.)
39. Secret of Ishbal (10.07.2004, 25 min.)
40. The Scar (17.07.2004, 25 min.)
41. Holy Mother (24.07.2004, 25 min.)
42. His Name is Unknown (24.07.2004, 25 min.)
43. The Stray Dog (31.07.2004, 25 min.)
44. Hohenheim of Light (07.08.2004, 25 min.)
45. A Rotted Heart (21.08.2004, 25 min.)
46. Human Transmutation (28.08.2004, 25 min.)
47. Sealing the Homunculus (04.09.2004, 25 min.)
48. Goodbye (11.09.2004, 25 min.)
49. The Other Side of the Gate (18.09.2004, 25 min.)
50. Death (25.09.2004, 25 min.)
51. Laws and Promises (02.10.2004, 25 min.)',
                [],
                'item'
            ))
            ->setEpisodesNumber('51')
            ->setFileInfo($this->translator->trans('+ special', [], 'item'))
            ->setName($this->translator->trans('Fullmetal Alchemist', [], 'item'))
            ->setStudio($this->getStudio('Bones'))
            ->setType($this->getType('tv'))
            ->setSummary($this->translator->trans(
                'The rules of alchemy state that to gain something, one must lose something of equal value. '
                .'Alchemy is the process of taking apart and reconstructing an object into a different entity, '
                .'with the rules of alchemy to govern this procedure. However, there exists an object that can '
                .'bring any alchemist above these rules, the object known as the Philosopher\'s Stone. The young '
                .'Edward Elric is a particularly talented alchemist who through an accident years back lost his '
                .'younger brother Alphonse and one of his legs. Sacrificing one of his arms as well, he used '
                .'alchemy to bind his brother\'s soul to a suit of armor. This lead to the beginning of their '
                .'journey to restore their bodies, in search for the legendary Philosopher\'s Stone.',
                [],
                'item'
            ))
            ->addGenre($this->getGenre('Adventure'))
            ->addGenre($this->getGenre('Drama'))
            ->addGenre($this->getGenre('Shounen'))
            ->addGenre($this->getGenre('Fantasy'))
            ->addSource((new Source())->setUrl('http://www.animenewsnetwork.com/encyclopedia/anime.php?id=2960'))
            ->addSource((new Source())->setUrl('http://anidb.net/perl-bin/animedb.pl?show=anime&aid=979'))
            ->addSource((new Source())->setUrl('http://cal.syoboi.jp/tid/134/time'))
            ->addSource((new Source())->setUrl('http://www.allcinema.net/prog/show_c.php?num_c=241943'))
            ->addSource((new Source())->setUrl('http://www1.vecceed.ne.jp/~m-satomi/FULLMETALALCHEMIST.html'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/121/Fullmetal_Alchemist'))
            ->addSource((new Source())->setUrl('http://myanimelist.net/anime/121/'))
            ->addSource((new Source())->setUrl('http://en.wikipedia.org/wiki/Fullmetal_Alchemist'))
            ->addSource((new Source())->setUrl('http://ru.wikipedia.org/wiki/Fullmetal_Alchemist'))
            ->addSource((new Source())->setUrl('http://ja.wikipedia.org/wiki/%E9%8B%BC%E3%81%AE%E9%8C%AC%E9%87%91%E8%A1%93%E5%B8%AB_%28%E3%82%A2%E3%83%8B%E3%83%A1%29'))
            ->addSource((new Source())->setUrl('http://oboi.kards.ru/?act=search&level=6&search_str=FullMetal%20Alchemist'))
            ->addSource((new Source())->setUrl('http://www.fansubs.ru/base.php?id=124'))
            ->addSource((new Source())->setUrl('http://www.world-art.ru/animation/animation.php?id=2368'))
            ->addSource((new Source())->setUrl('http://shikimori.org/animes/121-fullmetal-alchemist'));

        if ($item->getName() != 'Fullmetal Alchemist') {
            $item->addName((new Name())->setName('Fullmetal Alchemist'));
        }

        return $item
            ->addName((new Name())->setName('Hagane no Renkin Jutsushi'))
            ->addName((new Name())->setName('Hagane no Renkinjutsushi'))
            ->addName((new Name())->setName('Full Metal Alchemist'))
            ->addName((new Name())->setName('Hagaren'))
            ->addName((new Name())->setName('鋼の錬金術師'));
    }

    /**
     * @param Storage $storage
     *
     * @return Item
     */
    public function setStorage(Storage $storage)
    {
        $this->getItem()->setPath($storage->getPath().'Fullmetal Alchemist (2003) [TV]'.DIRECTORY_SEPARATOR);

        return parent::setStorage($storage);
    }
}
