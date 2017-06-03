<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Service;

use AnimeDb\Bundle\CatalogBundle\Service\Storage\FilenameCleaner;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

class FilenameCleanerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FilenameCleaner
     */
    protected $cleaner;

    /**
     * @var Filesystem
     */
    protected $fs;

    /**
     * @var string
     */
    protected $root;

    protected function setUp()
    {
        $this->root = sys_get_temp_dir().'/tests/';
        $this->fs = new Filesystem();
        $this->fs->mkdir($this->root);

        $this->cleaner = new FilenameCleaner();
    }

    protected function tearDown()
    {
        $this->fs->remove($this->root);
    }

    /**
     * @return array
     */
    public function getCleanFiles()
    {
        return [
            [
                'Sailor Moon',
                'Sailor Moon (1992) [TV]',
                true,
            ],
            [
                'Bleach',
                'Bleach (2004-2012) [TV] HDRip 720p',
                true,
            ],
            [
                'Rybka Pon\'o na Utjose',
                'Rybka Pon\'o na Utjose.(2008).BDRip.1080p.(DVD9).[NoLimits-Team].mkv',
            ],
            [
                'Howls Moving Castle',
                'Howls Moving Castle_HDRip_sub__[scarabey.org].avi',
            ],
            // from issues #160
            [
                'Mobile Suit Gundam Thunderbolt December Sky',
                '[Anime Land] Mobile Suit Gundam Thunderbolt December Sky (Dual Audio) (BDRip 720p Hi10P QAACx2) [EE69A6C6].mkv',
            ],
            [
                'Haikyuu!! Second Season - 14',
                '[Commie] Haikyuu!! Second Season - 14 [55DED0D6].mkv',
            ],
            [
                'Battery - 02',
                '[HorribleSubs] Battery - 02 [720p].mkv',
            ],
            [
                'Kono Bijutsubu ni wa Mondai ga Aru! - 03',
                '[HorribleSubs] Kono Bijutsubu ni wa Mondai ga Aru! - 03 [1080p].mkv',
            ],
            [
                'Mahou Shoujo Naria Girls - 03',
                '[HorribleSubs] Mahou Shoujo Naria Girls - 03 [1080p].mkv',
            ],
            [
                'Planetarian - 03',
                '[HorribleSubs] Planetarian - 03 [1080p].mkv',
            ],
            [
                'Fate Kaleid Liner Prisma Illya 3rei!! - 03',
                '[Impatience] Fate Kaleid Liner Prisma Illya 3rei!! - 03 [720p][A163DBD5].mkv',
            ],
            [
                'Onara Gorou - 02',
                '[Kaitou]_Onara_Gorou_-_02_[720p][10bit][40F0D37C].mkv',
            ],
            [
                'Ozmafia!! - 03',
                '[Kaitou]_Ozmafia!!_-_03_[720p][10bit][CDE45C5F].mkv',
            ],
        ];
    }

    /**
     * @dataProvider getCleanFiles
     *
     * @param string $expected
     * @param string $filename
     * @param bool $is_dir
     */
    public function testClean($expected, $filename, $is_dir = false)
    {
        if ($is_dir) {
            $this->fs->mkdir($this->root.$filename);
        } else {
            $this->fs->touch($this->root.$filename);
        }

        $file = new SplFileInfo($this->root.$filename, '', '');

        $this->assertEquals($expected, $this->cleaner->clean($file));
    }
}
