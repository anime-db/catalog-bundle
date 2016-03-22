<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Service;

use AnimeDb\Bundle\CatalogBundle\Service\StorageScanner;

/**
 * Test storage scanner
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Service
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class StorageScannerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test export
     */
    public function testExport()
    {
        $command = $this->getMockBuilder('\AnimeDb\Bundle\AppBundle\Service\CommandExecutor')
            ->disableOriginalConstructor()
            ->getMock();
        $fs = $this->getMock('\Symfony\Component\Filesystem\Filesystem');
        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $output = '/output/%s.log';
        $progress = '/progress/%s.log';
        $pattern = 'php app/console animedb:scan-storage --no-ansi --export=%s %s >%s 2>&1';

        $storage
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->will($this->returnValue(5));
        $fs
            ->expects($this->once())
            ->method('mkdir')
            ->with([dirname($output), dirname($progress)], 0755);
        $command
            ->expects($this->once())
            ->method('send')
            ->with(sprintf(
                $pattern,
                sprintf($progress, $storage->getId()),
                $storage->getId(),
                sprintf($output, $storage->getId())
            ));

        $scanner = new StorageScanner($command, $fs, $output, $progress);
        $scanner->export($storage);
    }
}
