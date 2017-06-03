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

class ScanExecutorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CommandExecutor
     */
    private $command;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Filesystem
     */
    private $fs;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Storage
     */
    private $storage;

    protected function setUp()
    {
        $this->command = $this
            ->getMockBuilder('\AnimeDb\Bundle\AppBundle\Service\CommandExecutor')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->fs = $this->getMock('\Symfony\Component\Filesystem\Filesystem');
        $this->storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
    }

    public function testExport()
    {
        $storage_id = 5;
        $output = '/output/%s.log';
        $progress = '/progress/%s.log';
        $pattern = 'php app/console animedb:scan-storage --no-ansi --force --export=%s %s >%s 2>&1';

        $this->storage
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->will($this->returnValue($storage_id));

        $this->fs
            ->expects($this->once())
            ->method('mkdir')
            ->with([dirname($output), dirname($progress)], 0755);
        $this->fs
            ->expects($this->once())
            ->method('remove')
            ->with([
                sprintf($output, $storage_id),
                sprintf($progress, $storage_id),
            ]);

        $this->command
            ->expects($this->once())
            ->method('send')
            ->with(sprintf(
                $pattern,
                sprintf($progress, $storage_id),
                $storage_id,
                sprintf($output, $storage_id)
            ));

        $scanner = new ScanExecutor($this->command, $this->fs, $output, $progress);
        $scanner->export($this->storage);
    }
}
