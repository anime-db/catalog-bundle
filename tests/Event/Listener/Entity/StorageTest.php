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

use AnimeDb\Bundle\CatalogBundle\Event\Listener\Entity\Storage;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Test storage entity listener
 *
 * @package AnimeDb\Bundle\CatalogBundle\Tests\Console\Progress
 * @author  Peter Gribanov <info@peter-gribanov.ru>
 */
class StorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Root dir
     *
     * @var string
     */
    protected $root;

    /**
     * Filesystem
     *
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $fs;

    /**
     * Real filesystem
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    protected $real_fs;

    /**
     * Storage
     *
     * @var \AnimeDb\Bundle\CatalogBundle\Event\Listener\Entity\Storage
     */
    protected $storage;

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->root = sys_get_temp_dir().'/test/';
        $this->fs = $this->getMock('\Symfony\Component\Filesystem\Filesystem');
        $this->storage = new Storage($this->fs);
        $this->real_fs = new Filesystem();
    }

    /**
     * (non-PHPdoc)
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        $this->real_fs->remove($this->root);
    }

    /**
     * Get event listeners
     *
     * @return array
     */
    public function getEventListeners()
    {
        return [
            ['postPersist'],
            ['postRemove'],
            ['postUpdate'],
        ];
    }

    /**
     * Test entity is not a storage
     *
     * @dataProvider getEventListeners
     *
     * @param string $method
     */
    public function testNoStorage($method)
    {
        $this->fs
            ->expects($this->never())
            ->method('exists');
        call_user_func([$this->storage, $method], $this->getArgs(new \stdClass()));
    }

    /**
     * Test post persist no path
     */
    public function testPostPersistNoPath()
    {
        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $storage
            ->expects($this->once())
            ->method('getPath')
            ->willReturn('foo');
        $this->fs
            ->expects($this->at(0))
            ->method('exists')
            ->willReturn(false)
            ->with('foo');
        $this->fs
            ->expects($this->never())
            ->method('dumpFile');
        $this->storage->postPersist($this->getArgs($storage));
    }

    /**
     * Test post persist file exists
     */
    public function testPostPersistFileExists()
    {
        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $storage
            ->expects($this->atLeastOnce())
            ->method('getPath')
            ->willReturn('foo');
        $this->fs
            ->expects($this->at(0))
            ->method('exists')
            ->willReturn(true)
            ->with('foo');
        $this->fs
            ->expects($this->at(1))
            ->method('exists')
            ->willReturn(true)
            ->with('foo'.Storage::ID_FILE);
        $this->fs
            ->expects($this->never())
            ->method('dumpFile');
        $this->storage->postPersist($this->getArgs($storage));
    }

    /**
     * Test post persist
     */
    public function testPostPersist()
    {
        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $storage
            ->expects($this->atLeastOnce())
            ->method('getPath')
            ->willReturn('foo');
        $storage
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(123);
        $this->fs
            ->expects($this->at(0))
            ->method('exists')
            ->willReturn(true)
            ->with('foo');
        $this->fs
            ->expects($this->at(1))
            ->method('exists')
            ->willReturn(false)
            ->with('foo'.Storage::ID_FILE);
        $this->fs
            ->expects($this->once())
            ->method('dumpFile')
            ->with('foo'.Storage::ID_FILE, 123, 0666);
        $this->storage->postPersist($this->getArgs($storage));
    }

    /**
     * Test post remove no path
     */
    public function testPostRemoveNoPath()
    {
        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $storage
            ->expects($this->atLeastOnce())
            ->method('getPath')
            ->willReturn('foo');
        $this->fs
            ->expects($this->at(0))
            ->method('exists')
            ->willReturn(false)
            ->with('foo'.Storage::ID_FILE);
        $this->fs
            ->expects($this->never())
            ->method('remove');
        $this->storage->postRemove($this->getArgs($storage));
    }

    /**
     * Test post remove bad file
     */
    public function testPostRemoveBadFile()
    {
        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $storage
            ->expects($this->atLeastOnce())
            ->method('getPath')
            ->willReturn($this->root);
        $storage
            ->expects($this->once())
            ->method('getId')
            ->willReturn(123);
        $this->fs
            ->expects($this->at(0))
            ->method('exists')
            ->willReturn(true)
            ->with($this->root.Storage::ID_FILE);
        $this->fs
            ->expects($this->never())
            ->method('remove');

        $this->real_fs->dumpFile($this->root.Storage::ID_FILE, 456);

        $this->storage->postRemove($this->getArgs($storage));
    }

    /**
     * Test post remove
     */
    public function testPostRemove()
    {
        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $storage
            ->expects($this->atLeastOnce())
            ->method('getPath')
            ->willReturn($this->root);
        $storage
            ->expects($this->once())
            ->method('getId')
            ->willReturn(123);
        $this->fs
            ->expects($this->at(0))
            ->method('exists')
            ->willReturn(true)
            ->with($this->root.Storage::ID_FILE);
        $this->fs
            ->expects($this->once())
            ->method('remove')
            ->with($this->root.Storage::ID_FILE);

        $this->real_fs->dumpFile($this->root.Storage::ID_FILE, 123);

        $this->storage->postRemove($this->getArgs($storage));
    }

    /**
     * Get args
     *
     * @param object $storage
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getArgs($storage)
    {
        $args = $this->getMockBuilder('\Doctrine\ORM\Event\LifecycleEventArgs')
            ->disableOriginalConstructor()
            ->getMock();
        $args
            ->expects($this->atLeastOnce())
            ->method('getEntity')
            ->willReturn($storage);
        return $args;
    }

    /**
     * Test post update no file
     */
    public function testPostUpdateNoFile()
    {
        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $storage
            ->expects($this->atLeastOnce())
            ->method('getPath')
            ->willReturn('baz');
        $storage
            ->expects($this->once())
            ->method('getOldPaths')
            ->willReturn(['foo', 'bar']);
        $this->fs
            ->expects($this->at(0))
            ->method('exists')
            ->willReturn(false)
            ->with('foo'.Storage::ID_FILE);
        $this->fs
            ->expects($this->at(1))
            ->method('exists')
            ->willReturn(false)
            ->with('bar'.Storage::ID_FILE);
        $this->fs
            ->expects($this->at(2))
            ->method('exists')
            ->willReturn(false)
            ->with('baz');
        $this->fs
            ->expects($this->never())
            ->method('remove');

        $this->storage->postUpdate($this->getArgs($storage));
    }

    /**
     * Test post update bad id
     */
    public function testPostUpdateBadId()
    {
        $file1 = $this->root.'foo/'.Storage::ID_FILE;
        $file2 = $this->root.'bar/'.Storage::ID_FILE;

        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $storage
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(123);
        $storage
            ->expects($this->atLeastOnce())
            ->method('getPath')
            ->willReturn('baz');
        $storage
            ->expects($this->once())
            ->method('getOldPaths')
            ->willReturn([dirname($file1).'/', dirname($file2).'/']);
        $this->fs
            ->expects($this->atLeastOnce())
            ->method('exists')
            ->willReturn(true);
        $this->fs
            ->expects($this->never())
            ->method('remove');

        $this->real_fs->dumpFile($file1, 456);
        $this->real_fs->dumpFile($file2, 456);

        $this->storage->postUpdate($this->getArgs($storage));
    }

    /**
     * Test post update
     */
    public function testPostUpdate()
    {
        $file1 = $this->root.'foo/'.Storage::ID_FILE;
        $file2 = $this->root.'bar/'.Storage::ID_FILE;

        $storage = $this->getMock('\AnimeDb\Bundle\CatalogBundle\Entity\Storage');
        $storage
            ->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(123);
        $storage
            ->expects($this->atLeastOnce())
            ->method('getPath')
            ->willReturn('baz');
        $storage
            ->expects($this->once())
            ->method('getOldPaths')
            ->willReturn([dirname($file1).'/', dirname($file2).'/']);
        $this->fs
            ->expects($this->at(0))
            ->method('exists')
            ->willReturn(true)
            ->with($file1);
        $this->fs
            ->expects($this->at(2))
            ->method('exists')
            ->willReturn(true)
            ->with($file2);
        $this->fs
            ->expects($this->at(4))
            ->method('exists')
            ->willReturn(true)
            ->with('baz');
        $this->fs
            ->expects($this->at(5))
            ->method('exists')
            ->willReturn(false)
            ->with('baz'.Storage::ID_FILE);
        $this->fs
            ->expects($this->at(1))
            ->method('remove')
            ->with($file1);
        $this->fs
            ->expects($this->at(3))
            ->method('remove')
            ->with($file2);
        $this->fs
            ->expects($this->once())
            ->method('dumpFile')
            ->with('baz'.Storage::ID_FILE, 123, 0666);

        $this->real_fs->dumpFile($file1, 123);
        $this->real_fs->dumpFile($file2, 123);

        $this->storage->postUpdate($this->getArgs($storage));
    }
}