<?php
/**
 * AnimeDb package
 *
 * @package   AnimeDb
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/GPL-3.0 GPL v3
 */

namespace AnimeDb\Bundle\CatalogBundle\Tests\Event\Listener\Entity;

use AnimeDb\Bundle\CatalogBundle\Event\Listener\Entity\Downloader;
use AnimeDb\Bundle\CatalogBundle\Entity\Item;
use AnimeDb\Bundle\CatalogBundle\Entity\Image;

/**
 * Test entity downloader listener
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Event\Listener\Entity
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class DownloaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * LifecycleEventArgs
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $args;

    /**
     * Filesystem
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fs;

    /**
     * Download root dir
     *
     * @var string
     */
    protected $root = '/foo/';

    /**
     * Listener
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Event\Listener\Entity
     */
    protected $listener;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->fs = $this->getMock('\Symfony\Component\Filesystem\Filesystem');
        $this->args = $this->getMockBuilder('\Doctrine\ORM\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock();
        $this->listener = new Downloader($this->fs, $this->root);
    }

    /**
     * Get entity
     *
     * @return array
     */
    public function getEntity()
    {
        return [
            [$this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item'), ''],
            [$this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Image'), ''],
            [$this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item'), '/test'],
            [$this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Image'), '/test'],
            [$this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item'), '/tmp/test'],
            [$this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Image'), '/tmp/test'],
            [$this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item'), '/tmp/test.log'],
            [$this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Image'), '/tmp/test.log'],
            [$this->getMock('\stdClass'), '']
        ];
    }

    /**
     * Test pre persist
     *
     * @dataProvider getEntity
     *
     * @param \PHPUnit_Framework_MockObject_MockObject $entity
     * @param string $filename
     */
    public function testPrePersist(\PHPUnit_Framework_MockObject_MockObject $entity, $filename)
    {
        $this->args
            ->expects($this->once())
            ->method('getEntity')
            ->willReturn($entity);
        if ($entity instanceof Item || $entity instanceof Image) {
            $time = $this->getMock('\DateTime');
            $time
                ->expects($this->once())
                ->method('format')
                ->with('Y/m/d/His/')
                ->willReturn('some/path');
            if ($entity instanceof Item) {
                $entity
                    ->expects($this->once())
                    ->method('getDateAdd')
                    ->willReturn($time);
            } else {
                $item = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Item');
                $entity
                    ->expects($this->once())
                    ->method('getItem')
                    ->willReturn($item);
                $item
                    ->expects($this->once())
                    ->method('getDateAdd')
                    ->willReturn($time);
            }
            $entity
                ->expects($this->at(1))
                ->method('getFilename')
                ->willReturn($filename);
            if ($filename) {
                $entity
                    ->expects($this->at(2))
                    ->method('getFilename')
                    ->willReturn($filename);
            }
            if (strpos($filename, 'tmp') !== false) {
                $entity
                    ->expects($this->at(3))
                    ->method('getFilename')
                    ->willReturn($filename);
                $entity
                    ->expects($this->at(6))
                    ->method('getFilename')
                    ->willReturn('new_filename');
                $entity
                    ->expects($this->once())
                    ->method('setFilename')
                    ->with('some/path'.pathinfo($filename, PATHINFO_BASENAME));
                $entity
                    ->expects($this->once())
                    ->method('getDownloadPath')
                    ->willReturn('web');
                $this->fs
                    ->expects($this->once())
                    ->method('copy')
                    ->with($this->root.'web/'.$filename, $this->root.'web/new_filename', true);
            }
        } else {
            $entity
                ->expects($this->never())
                ->method('getFilename');
        }

        $this->listener->prePersist($this->args);
    }

}
