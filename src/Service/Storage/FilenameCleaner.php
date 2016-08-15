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

        $name = preg_replace('/\[[^\[\]]*\]/', '', $name); // remove [...]
        $name = preg_replace('/\([^\(\)]*\)/', '', $name); // remove (...)
        $name = trim($name);

        return $name;
    }
}
