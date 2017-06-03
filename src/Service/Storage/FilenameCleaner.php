<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Service\Storage;

use Symfony\Component\Finder\SplFileInfo;

class FilenameCleaner
{
    /**
     * @var array
     */
    protected $meta = [
        // ripping
        'CamRip',
        'Cam',
        'Telesync',
        'TS',
        'Telecine',
        'TC',
        'Super Telesync',
        'SuperTS',
        'Super-TS',
        'VHS-Rip',
        'VHSRip',
        'Screener',
        'Scr',
        'VHS-Screener',
        'VHSScr',
        'PPVRip',
        'DVD-Screener',
        'DVDScr',
        'TV-Rip',
        'TVRip',
        'Sat-Rip',
        'SatRip',
        'HDTV-Rip',
        'HDTVRip',
        'PDTVRip',
        'HDRip',
        'BDRip',
        'DVD-Rip',
        'DVDRip',
        'LaserDisc-RIP',
        'LDRip',
        'Workprint',
        'WP',
        'WebRip',
        'Web-DL',
        'Web-DLRip',
        'Remux',
        'DCPrip',
        // DVD
        'DVD',
        'DVD5',
        'DVD9',
        'DVD10',
        'DVD18',
        // subtitles
        'Sub',
        'Subtitles',
        // display resolutions
        '8K',
        '5K',
        '4K',
        '1080p',
        '720p',
        '480p',
    ];

    public function __construct()
    {
        $this->meta = array_map('preg_quote', $this->meta);
    }

    /**
     * @param SplFileInfo $file
     *
     * @return string
     */
    public function clean(SplFileInfo $file)
    {
        $name = $file->getFilename();
        if ($file->isFile()) {
            $name = pathinfo($name, PATHINFO_FILENAME);
        }

        $name = str_replace("\t\n\r\0\x0B", ' ', $name); // remove control characters
        $name = str_replace('_', ' ', $name);

        $name = preg_replace('/\[[^\[\]]*\]/', ' ', $name); // remove [...]
        $name = preg_replace('/\([^\(\)]*\)/', ' ', $name); // remove (...)

        // remove all file meta data
        $reg = sprintf('/[ .,](?:%s)[ .,]/i', implode('|', $this->meta));
        while (preg_match($reg, $name.' ')) {
            $name = preg_replace($reg, ' ', $name.' ');
        }

        $name = str_replace('  ', ' ', $name); // remove double spaces
        $name = trim($name, ' .,-');

        return $name;
    }
}
