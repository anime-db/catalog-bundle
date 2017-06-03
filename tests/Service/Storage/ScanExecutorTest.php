<?php
/**
 * AnimeDb package.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Service;

use AnimeDb\Bundle\CatalogBundle\Service\Storage\ScanExecutor;
use AnimeDb\Bundle\AppBundle\Service\CommandExecutor;
use Symfony\Component\Filesystem\Filesystem;
use AnimeDb\Bundle\CatalogBundle\Entity\Storage;

/**
 * Test storage scanner.
 *
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class ScanExecutorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test export.
     */
    public function testExport()
    {
        $storage_id = 5;
        /* @var $command \PHPUnit_Framework_MockObject_MockObject|CommandExecutor */
        $command = $this
            ->getMockBuilder('\AnimeDb\Bundle\AppBundle\Service\CommandExecutor')
            ->disableOriginalConstructor()
            ->getMock();
        /* @var $fs \PHPUnit_Framework_MockObject_MockObject|Filesystem */
        $fs = $this->getMock('\Symfony\Component\Filesystem\Filesystem');
        /* @var $storage \PHPUnit_Framework_MockObject_MockObject|Storage */
        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $output = '/output/%s.log';
        $progress = '/progress/%s.log';
        $pattern = 'php app/console animedb:scan-storage --no-ansi --force --export=%s %s >%s 2>&1';

        $storage
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->will($this->returnValue($storage_id));
        $fs
            ->expects($this->once())
            ->method('mkdir')
            ->with([dirname($output), dirname($progress)], 0755);
        $fs
            ->expects($this->once())
            ->method('remove')
            ->with([
                sprintf($output, $storage_id),
                sprintf($progress, $storage_id),
            ]);
        $command
            ->expects($this->once())
            ->method('send')
            ->with(sprintf(
                $pattern,
                sprintf($progress, $storage->getId()),
                $storage->getId(),
                sprintf($output, $storage->getId())
            ));

        $scanner = new ScanExecutor($command, $fs, $output, $progress);
        $scanner->export($storage);
    }
}
